<?php

namespace UserFrosting;

/**
 * GroupController Class
 *
 * Controller class for /groups/* URLs.  Handles group-related activities, including listing groups, CRUD for groups, etc.
 *
 * @package UserFrosting
 */
class PromotionController extends \UserFrosting\BaseController {

    /**
     * Create a new GroupController object.
     *
     * @param UserFrosting $app The main UserFrosting app.
     */
    public function __construct($app){
        $this->_app = $app;
    }

    /**
     * Renders the group listing page.
     *
     * This page renders a table of user groups, with dropdown menus for modifying those groups.
     * This page requires authentication (and should generally be limited to admins or the root user).
     * Request type: GET
     * @todo implement interface to modify authorization hooks and permissions
     */
    public function pagePromotion($promotion_id){

        $promotion = Promotion::find($promotion_id);
        if(!$promotion){
            echo 'Not found Promotion';
            exit;
        }
        // Access-controlled resource
        if (!$this->_app->user->checkAccess('uri_promotion') && $this->_app->user->checkAccess("view_promotion_setting")){
            $this->_app->notFound();
        }

        // Get the alert message stream
        $ms = $this->_app->alerts;

//        if($company->status != '1'){
//            $message_error = $ms->getMessageTranslated("danger", "COMPANY_DISABLED");
////            $this->_app->showMessage($msg);
//            $template = "errors/disable.twig";
//            $this->_app->render($template, [
//                "message_error" => $message_error,
//                ]);
//        }else{
//        $template = "components/common/company-info-panel.twig";
//        }

        $get = $this->_app->request->get();

//        if (isset($get['render']))
//            $render = $get['render'];
//        else
//            $render = "modal";

        // Get a list of all groups
//        if ($this->_app->user->primary_group_id != '2'){
//            $groups = Group::where('id', '!=',  '2')->get();
//        }else{
//            $groups = Group::get();
//        }

        // Get a list of all locales
//        $locale_list = $this->_app->site->getLocales();

        // Determine which groups this user is a member of
//        $user_groups = $target_user->getGroups();
//        foreach ($groups as $group){
//            $group_id = $group->id;
//            $group_list[$group_id] = $group->export();
//            if (isset($user_groups[$group_id]))
//                $group_list[$group_id]['member'] = true;
//            else
//                $group_list[$group_id]['member'] = false;
//        }

//        if ($render == "modal")
//            $template = "components/common/user-info-modal.twig";
//        else
//            $template = "components/common/company-info-panel.twig";

        // Determine authorized fields
        $fields = ["id",
            "service_id",
            "title_th",
            "title_en",
            "title_cn",
            "detail_th",
            "detail_en",
            "detail_cn",
            "start_at",
            "end_at",
            "discount_price",
            "discount_percent",
            "type",
            "status"];
        $show_fields = [];
        $disabled_fields = [];
        $hidden_fields = [];
        foreach ($fields as $field){
            if ($this->_app->user->checkAccess("view_promotion_setting", ["property" => $field]))
                $disabled_fields[] = $field;
            else
                $hidden_fields[] = $field;
        }

        // Always disallow editing last_modified
        // $disabled_fields[] = "last_modified";

        // Load validator rules
        $schema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/promotion-update.json");
        $this->_app->jsValidator->setSchema($schema);

        $this->_app->render('promotion/promotion-info.twig', [
            "box_id" => 'view-promotion',
            "alerts_id" => 'form-view-promotion-alerts',
            "promotion" => $promotion,
            "fields" => [
                "disabled" => $disabled_fields,
                "hidden" => $hidden_fields
            ],
            "buttons" => [
                "hidden" => [
                    "submit", "cancel", "delete"
                ]
            ],
            "validators" => $this->_app->jsValidator->rules()
        ]);
    }

    public function pagePromotions(){
        // Access-controlled page

        if ($this->_app->user->primary_group_id != 2){

//            $services_id = $this->_app->user->getServiceIds();

            $promotion = Promotion::where('company_id', $this->_app->user->company_id)->get();
            // print_r($services);
            // exit();
        }else{
            $promotion = Promotion::get();
        }

        $this->_app->render('promotion/promotions.twig', [
            "promotions" => $promotion
        ]);
    }

    public function formPromotionEdit($promotion_id){
        // Get the user to edit
//        $target_user = User::find($user_id);
        $promotion = Promotion::find($promotion_id);
        if(!$promotion){
            echo 'Not found service';
            exit;
        }

        // Access-controlled resource
        if (!$this->_app->user->checkAccess('uri_promotion') && !$this->_app->user->checkAccess("update_promotion_setting")){
            $this->_app->notFound();
        }

        // Get the alert message stream
        $ms = $this->_app->alerts;

        $get = $this->_app->request->get();

        // Get a list of all branches
//        if ($this->_app->user->primary_group_id != '2'){
        $services = Service::where('company_id', $this->_app->user->company_id)->get();
//        }else{
//            $branch = Branch::get();
//        }

        // Get a list of all locales
        $locale_list = $this->_app->site->getLocales();

        // Determine which groups this user is a member of

        $promotion_service = $promotion->getServices();
        foreach ($services as $service){
            $service_id = $service->id;
            $service_list[$service_id] = $service->export();
            if (isset($promotion_service[$service_id]))
                $service_list[$service_id]['member'] = true;
            else
                $service_list[$service_id]['member'] = false;
        }

        if (isset($get['render']))
            $render = $get['render'];
        else
            $render = "modal";

        if ($render == "modal")
            $template = "components/common/promotion-info-modal.twig";
        else
            $template = "components/common/promotion-info-panel.twig";

        // Get a list of all groups
//        if ($this->_app->user->primary_group_id != '2'){
//            $groups = Group::where('id', '!=',  '2')->get();
//        }else{
//            $groups = Group::get();
//        }

        // Get a list of all locales
//        $locale_list = $this->_app->site->getLocales();

        // Determine which groups this user is a member of
//        $user_groups = $target_user->getGroups();
//        foreach ($groups as $group){
//            $group_id = $group->id;
//            $group_list[$group_id] = $group->export();
//            if (isset($user_groups[$group_id]))
//                $group_list[$group_id]['member'] = true;
//            else
//                $group_list[$group_id]['member'] = false;
//        }

//        if ($render == "modal")
//            $template = "components/common/user-info-modal.twig";
//        else
//            $template = "components/common/company-info-panel.twig";

        // Determine authorized fields
        $fields = ["id",
            "service_id",
            "title_th",
            "title_en",
            "title_cn",
            "detail_th",
            "detail_en",
            "detail_cn",
            "start_at",
            "end_at",
            "discount_price",
            "discount_percent",
            "type",
            "status"];
        $show_fields = [];
        $disabled_fields = [];
        $hidden_fields = [];
        foreach ($fields as $field){
            if ($this->_app->user->checkAccess("update_promotion_setting", [ "property" => $field]))
                $show_fields[] = $field;
            else if ($this->_app->user->checkAccess("view_promotion_setting", [ "property" => $field]))
                $disabled_fields[] = $field;
            else
                $hidden_fields[] = $field;
        }

        // Always disallow editing last_modified
        // $disabled_fields[] = "last_modified";

        // Load validator rules
        $schema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/promotion-update.json");
        $this->_app->jsValidator->setSchema($schema);

        $this->_app->render($template, [
            "box_id" => $get['box_id'],
            "box_title" => "Edit Promotion",
            "submit_button" => "Update Promotion",
            "form_action" => $this->_app->site->uri['public'] . "/promotion/b/$promotion_id",
            "promotion" => $promotion,
            "services" => $service_list,
            "fields" => [
                "disabled" => $disabled_fields,
                "hidden" => $hidden_fields
            ],
            "buttons" => [
                "hidden" => [
                    "edit", "enable", "delete"
                ]
            ],
            "validators" => $this->_app->jsValidator->rules()
        ]);
    }

    public function formPromotionCreate(){

        if (!$this->_app->user->checkAccess('create_promotion')){
            $this->_app->notFound();
        }

        // Get the user to edit
//        $target_user = User::find($user_id);
        // $branch = Branch::find($branch_id);
        // if(!$branch){
        //     echo 'Not found Branch';
        //     exit;
        // }

        // Access-controlled resource
        // if (!$this->_app->user->checkAccess('uri_branch') && $this->_app->user->checkAccess("update_branch_setting", ["self_company" => $branch])){
        //     $this->_app->notFound();
        // }

        // Get the alert message stream
        $ms = $this->_app->alerts;

        $get = $this->_app->request->get();

        if (isset($get['render']))
            $render = $get['render'];
        else
            $render = "modal";

        if ($render == "modal")
            $template = "components/common/promotion-info-modal.twig";
        else
            $template = "components/common/promotion-info-panel.twig";

        $data['company_id'] = $this->_app->user->company_id;

        $promotion = new Promotion($data);

        $services = Service::where('company_id', $this->_app->user->company_id)->get();


//        $service_promotion = $promotion->getServices();
        foreach ($services as $service){
            $service_id = $service->id;
            $service_list[$service_id] = $service->export();
            if (isset($service_promotion[$service_id]))
                $service_list[$service_id]['member'] = true;
            else
                $service_list[$service_id]['member'] = false;
        }

        // Get a list of all locales
//        $locale_list = $this->_app->site->getLocales();

        // Determine which groups this user is a member of
//        $user_groups = $target_user->getGroups();
//        foreach ($groups as $group){
//            $group_id = $group->id;
//            $group_list[$group_id] = $group->export();
//            if (isset($user_groups[$group_id]))
//                $group_list[$group_id]['member'] = true;
//            else
//                $group_list[$group_id]['member'] = false;
//        }

//        if ($render == "modal")
//            $template = "components/common/user-info-modal.twig";
//        else
//            $template = "components/common/company-info-panel.twig";

        // Determine authorized fields
        $fields = ["id",
            "service_id",
            "title_th",
            "title_en",
            "title_cn",
            "detail_th",
            "detail_en",
            "detail_cn",
            "start_at",
            "end_at",
            "discount_price",
            "discount_percent",
            "type",
            "status"];
        $show_fields = [];
        $disabled_fields = [];
        $hidden_fields = [];
        foreach ($fields as $field){
            if ($this->_app->user->checkAccess("update_promotion_setting", [ "property" => $field]))
                $show_fields[] = $field;
            else
                $disabled_fields[] = $field;
        }

        // Always disallow editing last_modified
        // $disabled_fields[] = "last_modified";

        // Load validator rules
        $schema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/promotion-create.json");
        $this->_app->jsValidator->setSchema($schema);

        $this->_app->render($template, [
            "box_id" => $get['box_id'],
            "box_title" => "Create Promotion",
            "submit_button" => "Create Promotion",
            "form_action" => $this->_app->site->uri['public'] . "/promotions",
            "promotion" => $promotion,
            "services" => $service_list,
            "fields" => [
                "disabled" => $disabled_fields,
                "hidden" => $hidden_fields
            ],
            "buttons" => [
                "hidden" => [
                    "edit", "enable", "delete", "activate"
                ]
            ],
            "validators" => $this->_app->jsValidator->rules()
        ]);
    }

    public function pageGroupAuthorization($group_id) {
        // Access-controlled page
        if (!$this->_app->user->checkAccess('uri_authorization_settings')){
            $this->_app->notFound();
        }

        $group = Group::find($group_id);

        // Load all auth rules
        $rules = GroupAuth::where('group_id', $group_id)->get();

        $this->_app->render('config/authorization.twig', [
            "group" => $group,
            "rules" => $rules
        ]);

    }

    /**
     * Renders the form for creating a new group.
     *
     * This does NOT render a complete page.  Instead, it renders the HTML for the form, which can be embedded in other pages.
     * The form can be rendered in "modal" (for popup) or "panel" mode, depending on the value of the GET parameter `render`
     * This page requires authentication (and should generally be limited to admins or the root user).
     * Request type: GET
     */
    public function formCompanyCreate(){
        // Access-controlled resource
        if (!$this->_app->user->checkAccess('create_company')){
            $this->_app->notFound();
        }

        $get = $this->_app->request->get();

        if (isset($get['render']))
            $render = $get['render'];
        else
            $render = "modal";

        // Get a list of all themes
        $theme_list = $this->_app->site->getThemes();

//        // Set default values
//        $data['is_default'] = "0";
//        // Set default title for new users
//        $data['new_user_title'] = "New User";
//        // Set default theme
//        $data['theme'] = "default";
//        // Set default icon
//        $data['icon'] = "fa fa-user";
//        // Set default landing page
//        $data['landing_page'] = "dashboard";

        // Create a dummy Group to prepopulate fields
        $company = new Company();

//        if ($render == "modal")
//            $template = "components/common/group-info-modal.twig";
//        else
//            $template = "components/common/group-info-panel.twig";

        $template = "components/common/company-info-panel.twig";

        // Determine authorized fields
        $fields = ['company_name_th', 'company_name_en', 'company_detail_th', 'company_detail_en', 'phone', 'address', 'lat', 'lng', 'type'];
        $show_fields = [];
        $disabled_fields = [];
        $hidden_fields = [];
        foreach ($fields as $field){
            if ($this->_app->user->checkAccess("update_company_setting", ["property" => $field]))
                $show_fields[] = $field;
            else
                $disabled_fields[] = $field;
        }

        // Load validator rules
        $schema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/company-create.json");
        $this->_app->jsValidator->setSchema($schema);

        $this->_app->render($template, [
//            "box_id" => $get['box_id'],
            "box_id" => "create_company",
            "box_title" => "New Company",
            "submit_button" => "Create company",
            "form_action" => $this->_app->site->uri['public'] . "/companies",
            "company" => $company,
//            "themes" => $theme_list,
            "fields" => [
                "disabled" => $disabled_fields,
                "hidden" => $hidden_fields
            ],
            "buttons" => [
                "hidden" => [
                    "edit", "enable", "delete"
                ]
            ],
            "validators" => $this->_app->jsValidator->rules()
        ]);
    }

    /**
     * Renders the form for editing an existing group.
     *
     * This does NOT render a complete page.  Instead, it renders the HTML for the form, which can be embedded in other pages.
     * The form can be rendered in "modal" (for popup) or "panel" mode, depending on the value of the GET parameter `render`.
     * Any fields that the user does not have permission to modify will be automatically disabled.
     * This page requires authentication (and should generally be limited to admins or the root user).
     * Request type: GET
     * @param int $group_id the id of the group to edit.
     */
    public function formGroupEdit($group_id){
        // Access-controlled resource
        if (!$this->_app->user->checkAccess('uri_groups')){
            $this->_app->notFound();
        }

        $get = $this->_app->request->get();

        if (isset($get['render']))
            $render = $get['render'];
        else
            $render = "modal";

        // Get the group to edit
        $group = Group::find($group_id);

        // Get a list of all themes
        $theme_list = $this->_app->site->getThemes();

        if ($render == "modal")
            $template = "components/common/group-info-modal.twig";
        else
            $template = "components/common/group-info-panel.twig";

        // Determine authorized fields
        $fields = ['name', 'new_user_title', 'landing_page', 'theme', 'is_default'];
        $show_fields = [];
        $disabled_fields = [];
        $hidden_fields = [];
        foreach ($fields as $field){
            if ($this->_app->user->checkAccess("update_group_setting", ["property" => $field]))
                $show_fields[] = $field;
            else if ($this->_app->user->checkAccess("view_group_setting", ["property" => $field]))
                $disabled_fields[] = $field;
            else
                $hidden_fields[] = $field;
        }

        // Load validator rules
        $schema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/group-update.json");
        $this->_app->jsValidator->setSchema($schema);

        $this->_app->render($template, [
            "box_id" => $get['box_id'],
            "box_title" => "Edit Group",
            "submit_button" => "Update group",
            "form_action" => $this->_app->site->uri['public'] . "/groups/g/$group_id",
            "group" => $group,
            "themes" => $theme_list,
            "fields" => [
                "disabled" => $disabled_fields,
                "hidden" => $hidden_fields
            ],
            "buttons" => [
                "hidden" => [
                    "edit", "delete"
                ]
            ],
            "validators" => $this->_app->jsValidator->rules()
        ]);
    }

    /**
     * Processes the request to create a new group.
     *
     * Processes the request from the group creation form, checking that:
     * 1. The group name is not already in use;
     * 2. The user has the necessary permissions to update the posted field(s);
     * 3. The submitted data is valid.
     * This route requires authentication (and should generally be limited to admins or the root user).
     * Request type: POST
     * @see formGroupCreate
     */
    public function createGroup(){
        $post = $this->_app->request->post();

        // DEBUG: view posted data
        //error_log(print_r($post, true));

        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/group-create.json");

        // Get the alert message stream
        $ms = $this->_app->alerts;

        // Access-controlled resource
        if (!$this->_app->user->checkAccess('create_group')){
            $ms->addMessageTranslated("danger", "ACCESS_DENIED");
            $this->_app->halt(403);
        }

        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Sanitize data
        $rf->sanitize();

        // Validate, and halt on validation errors.
        $error = !$rf->validate(true);

        // Get the filtered data
        $data = $rf->data();

        // Remove csrf_token from object data
        $rf->removeFields(['csrf_token']);

        // Perform desired data transformations on required fields.
        $data['name'] = trim($data['name']);
        $data['new_user_title'] = trim($data['new_user_title']);
        $data['landing_page'] = strtolower(trim($data['landing_page']));
        $data['theme'] = trim($data['theme']);
        $data['can_delete'] = 1;

        // Check if group name already exists
        if (Group::where('name', $data['name'])->first()){
            $ms->addMessageTranslated("danger", "GROUP_NAME_IN_USE", $post);
            $error = true;
        }

        // Halt on any validation errors
        if ($error) {
            $this->_app->halt(400);
        }

        // Set default values if not specified or not authorized
        if (!isset($data['theme']) || !$this->_app->user->checkAccess("update_group_setting", ["property" => "theme"]))
            $data['theme'] = "default";

        if (!isset($data['new_user_title']) || !$this->_app->user->checkAccess("update_group_setting", ["property" => "new_user_title"])) {
            // Set default title for new users
            $data['new_user_title'] = "New User";
        }

        if (!isset($data['landing_page']) || !$this->_app->user->checkAccess("update_group_setting", ["property" => "landing_page"])) {
            $data['landing_page'] = "dashboard";
        }

        if (!isset($data['icon']) || !$this->_app->user->checkAccess("update_group_setting", ["property" => "icon"])) {
            $data['icon'] = "fa fa-user";
        }

        if (!isset($data['is_default']) || !$this->_app->user->checkAccess("update_group_setting", ["property" => "is_default"])) {
            $data['is_default'] = "0";
        }

        // Create the group
        $group = new Group($data);

        // Store new group to database
        $group->store();

        // Success message
        $ms->addMessageTranslated("success", "GROUP_CREATION_SUCCESSFUL", $data);
    }

    /**
     * Processes the request to update an existing group's details.
     *
     * Processes the request from the group update form, checking that:
     * 1. The group name is not already in use;
     * 2. The user has the necessary permissions to update the posted field(s);
     * 3. The submitted data is valid.
     * This route requires authentication (and should generally be limited to admins or the root user).
     * Request type: POST
     * @param int $group_id the id of the group to edit.
     * @see formGroupEdit
     */

    public function createPromotion(){
        // Access-controlled resource
        if (!$this->_app->user->checkAccess('create_promotion')){
            $this->_app->notFound();
        }

        $post = $this->_app->request->post();

        // DEBUG: view posted data
        //error_log(print_r($post, true));

        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/promotion-create.json");

        // Get the alert message stream
        $ms = $this->_app->alerts;

        // Get the target group
//        $group = Group::find($company_id);
        // $branch = Branch::find($branch_id);

        // If desired, put route-level authorization check here

        // Remove csrf_token
        unset($post['csrf_token']);

        // Check authorization for submitted fields, if the value has been changed
//         foreach ($post as $name => $value) {
//             if (isset($branch->$name) && $post[$name] != $branch->$name){
//                 // Check authorization
//                 if (!$this->_app->user->checkAccess('update_company_setting', ['branch' => $branch, 'property' => $name])){
//                     $ms->addMessageTranslated("danger", "ACCESS_DENIED");
//                     $this->_app->halt(403);
//                 }
//             } else if (!isset($branch->$name)) {
// //                $ms->addMessageTranslated("danger", "NO_DATA".$name);
// //                $this->_app->halt(400);
//             }
//         }

        // Check that name is not already in use
//        if (isset($post['company_name_th']) && $post['company_name_th'] != $company->name && Company::where('company_name_th', $post['company_name_th'])->first()){
//            $ms->addMessageTranslated("danger", "GROUP_NAME_IN_USE", $post);
//            $this->_app->halt(400);
//        }

        // TODO: validate landing page route, theme, icon?

        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Sanitize
        $rf->sanitize();

        // Validate, and halt on validation errors.
        $error = !$rf->validate(true);

        // Get the filtered data
        $data = $rf->data();

        $rf->removeFields(['csrf_token']);


        // Halt on any validation errors
        if ($error) {
            $this->_app->halt(400);
        }

        // Create the user
        $promotion = new Promotion($data);

//            $branches = $service->getBranches();

        foreach ($data['services'] as $promotion_id => $is_member) {
            if ($is_member == "1"){
                $promotion->addService($promotion_id);
            }
        }

        // Update the group and generate success messages
        // foreach ($data as $name => $value){
        //     if ($value != $branch->$name){
        //         $branch->$name = $value;
        //         // Add any custom success messages here
        //     }
        // }
//            $service->company_id = $this->_app->user->company_id;
        $promotion->status = 1;
//            $service->lat = 1;
//            $service->lng = 1;
        // $branch->last_modified = NULL;
        $ms->addMessageTranslated("success", "GROUP_UPDATE", ["name" => $promotion->name]);
        $promotion->save();

    }

    public function updatePromotion($promotion_id){

        $post = $this->_app->request->post();

        // DEBUG: view posted data
        //error_log(print_r($post, true));

        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($this->_app->config('schema.path') . "/forms/promotion-update.json");

        // Get the alert message stream
        $ms = $this->_app->alerts;

        // Get the target group
//        $group = Group::find($company_id);
        $promotion = Promotion::find($promotion_id);

        // If desired, put route-level authorization check here

        // Remove csrf_token
        unset($post['csrf_token']);

        // Check authorization for submitted fields, if the value has been changed
        foreach ($post as $name => $value) {
            if (isset($promotion->$name) && $post[$name] != $promotion->$name){
                // Check authorization
                if (!$this->_app->user->checkAccess('update_promotion_setting', [ 'property' => $name])){
                    $ms->addMessageTranslated("danger", "ACCESS_DENIED");
                    $this->_app->halt(403);
                }
            } else if (!isset($promotion->$name)) {
//                $ms->addMessageTranslated("danger", "NO_DATA".$name);
//                $this->_app->halt(400);
            }
        }

        // Check that name is not already in use
//        if (isset($post['company_name_th']) && $post['company_name_th'] != $company->name && Company::where('company_name_th', $post['company_name_th'])->first()){
//            $ms->addMessageTranslated("danger", "GROUP_NAME_IN_USE", $post);
//            $this->_app->halt(400);$promotion
//        }

        // TODO: validate landing page route, theme, icon?

        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $post);

        // Sanitize
        $rf->sanitize();

        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            $this->_app->halt(400);
        }

        // Get the filtered data
        $data = $rf->data();

        $services = $promotion->getServices();

        if (isset($data['services'])){

            foreach ($data['services'] as $service_id => $is_member) {
                if ($is_member == "1" && !isset($services[$service_id])){
                    $promotion->addService($service_id);
                } else if ($is_member == "0" && isset($services[$service_id])){
                    $promotion->removeService($service_id);
                }
            }
            unset($data['services']);
        }

        // Update the group and generate success messages
        foreach ($data as $name => $value){
            if ($value != $promotion->$name){
                $promotion->$name = $value;
                // Add any custom success messages here
            }
        }
        if(isset($data['status'])){
            $promotion->status = $data['status'];
        }

//        $service->lat = 1;
//        $service->lng = 1;
        // $branch->last_modified = NULL;
        $ms->addMessageTranslated("success", "GROUP_UPDATE", ["name" => $promotion->name]);
        $promotion->save();

    }

    /**
     * Processes the request to delete an existing group.
     *
     * Deletes the specified group, removing associations with any users and any group-specific authorization rules.
     * Before doing so, checks that:
     * 1. The group is deleteable (as specified in the `can_delete` column in the database);
     * 2. The group is not currently set as the default primary group;
     * 3. The submitted data is valid.
     * This route requires authentication (and should generally be limited to admins or the root user).
     * Request type: POST
     * @param int $group_id the id of the group to delete.
     */
    public function deleteService($service_id){
        $post = $this->_app->request->post();

        // Get the target group
        $service = Service::find($service_id);

        // Get the alert message stream
        $ms = $this->_app->alerts;

        // Check authorization
        if (!$this->_app->user->checkAccess('delete_service', ['self_company' => $this->_app->user])){
            $ms->addMessageTranslated("danger", "ACCESS_DENIED");
            $this->_app->halt(403);
        }

//        // Check that we are allowed to delete this group
//        if ($service->can_delete == "0"){
//            $ms->addMessageTranslated("danger", "CANNOT_DELETE_GROUP", ["name" => $service->name]);
//            $this->_app->halt(403);
//        }

//        // Do not allow deletion if this group is currently set as the default primary group
//        if ($service->is_default == GROUP_DEFAULT_PRIMARY){
//            $ms->addMessageTranslated("danger", "GROUP_CANNOT_DELETE_DEFAULT_PRIMARY", ["name" => $group->name]);
//            $this->_app->halt(403);
//        }

        $ms->addMessageTranslated("success", "GROUP_DELETION_SUCCESSFUL", ["name" => $service->name]);
        $service->delete();       // TODO: implement Group function
        unset($service);
    }

}
