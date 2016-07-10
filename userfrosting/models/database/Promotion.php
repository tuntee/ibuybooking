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
class Promotion extends UFModel {

    /**
     * @var string The id of the table for the current model.
     */
    protected static $_table_id = "promotion";

    protected $_services;

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

    public function getServiceIds(){

        // Fetch from database, if not set
        if (!isset($this->_services)){

            $link_table = Database::getSchemaTable('service_promotion')->name;
            $result = Capsule::table($link_table)->select("service_id")->where("promotion_id", $this->id)->get();

            $this->_services = [];
            foreach ($result as $service){
                $this->_services[] = $service['service_id'];
            }
        }
        return $this->_services;
    }

    public function getServices(){
        $this->getServiceIds();

        // Return the array of group objects
        $result = Service::find($this->_services);

        $services = [];
        foreach ($result as $service){
            $services[$service->id] = $service;
        }
        return $services;
    }

    public function addService($service_id){
        $this->getServiceIds();

        // Return if user already in group
        if (in_array($service_id, $this->_services))
            return $this;

        // Next, check that the requested group actually exists
        if (!Service::find($service_id))
            throw new \Exception("The specified group_id ($service_id) does not exist.");

        // Ok, add to the list of groups
        $this->_services[] = $service_id;

        return $this;
    }

    public function removeService($service_id){
        // Fetch from database, if not set
        $this->getServiceIds();
//        unset($this->_services[2]);
        // Check that user not in group
        if (($key = array_search($service_id, $this->_services)) !== false) {
            unset($this->_services[$key]);
        }

        return $this;
    }

    private function syncCachedServices(){
        if (isset($this->_services)) {
            $link_table = Database::getSchemaTable('service_promotion')->name;
            return $this->belongsToMany('UserFrosting\Service', $link_table)->sync($this->_services);
        } else
            return false;
    }

    public function save(array $options = []){

// Synchronize model's service relations with database
        $this->syncCachedServices();

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
