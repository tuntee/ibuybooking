<?php

namespace UserFrosting;

use \Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Group Class
 *
 * Represents a group object as stored in the database.
 *
 * @package UserFrosting
 * @author Alex Weissman
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 * @property string name
 * @property string theme
 * @property string landing_page
 * @property string new_user_title
 * @property string icon
 * @property bool is_default
 * @property bool can_delete
 */
class Service extends UFModel {

    /**
     * @var string The id of the table for the current model.
     */ 
    protected static $_table_id = "service";

    protected $_branches;

    /**
     * Create a new Group object.
     *
     */
    public function __construct($properties = []) {
        parent::__construct($properties);
    }

    /**
     * Lazily load a collection of Users which belong to this group.
     */

    public function users(){
        $link_table = Database::getSchemaTable('group_user')->name;
        return $this->belongsToMany('UserFrosting\User', $link_table);
    }

    public function getBranchIds(){

        // Fetch from database, if not set
        if (!isset($this->_branches)){

            $link_table = Database::getSchemaTable('branch_service')->name;
            $result = Capsule::table($link_table)->select("branch_id")->where("service_id", $this->id)->get();

            $this->_branches = [];
            foreach ($result as $branch){
                $this->_branches[] = $branch['branch_id'];
            }
        }
        return $this->_branches;
    }

    public function getBranches(){
        $this->getBranchIds();

        // Return the array of group objects
        $result = Branch::find($this->_branches);

        $branches = [];
        foreach ($result as $branch){
            $branches[$branch->id] = $branch;
        }
        return $branches;
    }

    public function addBranch($branch_id){
        $this->getBranchIds();

        // Return if user already in group
        if (in_array($branch_id, $this->_branches))
            return $this;

        // Next, check that the requested group actually exists
        if (!Branch::find($branch_id))
            throw new \Exception("The specified group_id ($branch_id) does not exist.");

        // Ok, add to the list of groups
        $this->_branches[] = $branch_id;

        return $this;
    }

    public function removeBranch($branch_id){
        // Fetch from database, if not set
        $this->getBranchIds();
//        unset($this->_branches[2]);
        // Check that user not in group
        if (($key = array_search($branch_id, $this->_branches)) !== false) {
            unset($this->_branches[$key]);
        }

        return $this;
    }

    private function syncCachedBranches(){
        if (isset($this->_branches)) {
            $link_table = Database::getSchemaTable('branch_service')->name;
            return $this->belongsToMany('UserFrosting\Branch', $link_table)->sync($this->_branches);
        } else
            return false;
    }

    public function save(array $options = []){

// Synchronize model's branch relations with database
        $this->syncCachedBranches();

        return parent::save($options);
    }

    /**
     * Delete this group from the database, along with any linked user and authorization rules
     *
     */
    public function delete(){
        // Remove all user associations
//        $this->users()->detach();
//
//        // Remove all group auth rules
//        $auth_table = Database::getSchemaTable('authorize_group')->name;
//        Capsule::table($auth_table)->where("group_id", $this->id)->delete();
//
//        // Reassign any primary users to the current default primary group
//        $default_primary_group = Group::where('is_default', GROUP_DEFAULT_PRIMARY)->first();
//
//        $user_table = Database::getSchemaTable('user')->name;
//        Capsule::table($user_table)->where('primary_group_id', $this->id)->update(["primary_group_id" => $default_primary_group->id]);
//
//        // TODO: assign user to the default primary group as well?

        // Delete the group
        $result = parent::delete();

        return $result;
    }
}
