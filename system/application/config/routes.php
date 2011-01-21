<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

/* connect/type/slug/stream/type/page */

$route['encoding']										= "connect/encoding";

$route['default_controller']							= "home";
$route['scaffolding_trigger']							= "";

$route['notifications']									= 'notifications/index';
$route['notifications/send_emails']						= 'notifications/send_emails';
$route['notifications/send_emails/(:num)']				= 'notifications/send_emails/$1';
$route['notifications/send_digest_emails']				= 'notifications/send_digest_emails';
$route['notifications/send_digest_emails/(:num)']		= 'notifications/send_digest_emails/$1';

$route['login']											= 'account/login';
$route['account/logout']								= 'account/logout';
$route['service_info/(:num)']							= 'service_info/index/$1';

$route['notes']											= 'notes';

$route['watch/(:num)/note/save']						= 'watch/save_note/$1';
$route['watch/(:num)/note/view']						= 'watch/view_note/$1';
$route['watch/(:num)']									= 'watch/show/$1';
$route['watch']											= 'watch';

$route['notes/(:num)']									= 'notes/index/$1';
$route['notes/(:num)/delete']							= 'notes/delete/$1';

$route['api/connect/join/(:num)']						= 'connect/join/$1';
$route['api/connect/online']							= 'connect/groups_online';
$route['api/connect/users/online']						= 'connect/users_online';
$route['api/connect/users/online/(:num)']				= 'connect/users_online/$1';
$route['api/connect/share_post/(:num)/(:num)']			= 'connect/api_share_post/$1/$2';
$route['api/notes']										= 'notes/api';
$route['api/notifications/mark_as_read/(:num)']			= 'notifications/mark_as_read/$1';
$route['api/notifications/mark_all_read']				= 'notifications/mark_all_read';
$route['api/notifications/unread']						= 'notifications/unread_notifications';
$route['api/finder']									= 'connect/finder_api';
$route['api/email']										= 'home/email';

$route['connect']										= 'connect/index';
$route['connect/find']									= 'connect/find';
$route['connect/find/(:any)/(:any)']					= 'connect/find/$1/$2';
$route['connect/find/(:any)']							= 'connect/find/$1';
$route['connect/:any/(:any)/p/(:any)']					= 'connect/show_post/$1/$2';
$route['connect/:any/(:any)/images']					= 'connect/group_images/$1';
$route['connect/:any/(:any)/join/$2']					= 'connect/join_group_api/$1/$2';
$route['connect/:any/(:any)/stream/(:any)/(:num)']		= 'connect/item_list_ajax/$1/$2/$3';
$route['connect/:any/(:any)/settings']					= 'connect/settings/$1';
$route['connect/:any/(:any)']							= 'connect/show/$1';

$route['(groups|locations|ministries)/(:any)/p/(:any)/delete']						= 'connect/delete_post/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)/stick']						= 'connect/stick_post/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)/unstick']						= 'connect/unstick_post/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)/attending']					= 'connect/attending_event/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)/notattending']				= 'connect/not_attending_event/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)/edit']						= 'connect/edit_post/$2/$3';
$route['(groups|locations|ministries)/(:any)/p/(:any)']								= 'connect/show_post/$2/$3';
$route['(groups|locations|ministries)/(:any)/r/(:any)/delete']						= 'connect/delete_response/$2/$3';
$route['(groups|locations|ministries)/(:any)/s/(:any)/remove']						= 'connect/remove_shared/$2/$3';
$route['(groups|locations|ministries)/(:any)/images']								= 'connect/group_images/$2';
$route['(groups|locations|ministries)/(:any)/join']									= 'connect/join/$2';
$route['(groups|locations|ministries)/(:any)/join/$2']								= 'connect/join_group_api/$2/$3';
$route['(groups|locations|ministries)/(:any)/stream/(:any)/(:num)']					= 'connect/item_list_ajax/$2/$3/$4';
$route['(groups|locations|ministries)/(:any)/group_settings']						= 'connect/group_settings/$2';
$route['(groups|locations|ministries)/(:any)/settings']								= 'connect/settings/$2';
$route['(groups|locations|ministries)/(:any)/settings/members']						= 'connect/member_settings/$2';
$route['(groups|locations|ministries)/(:any)/settings/members/facilitator/(:num)']	= 'connect/member_settings_make_facilitator/$2/$3';
$route['(groups|locations|ministries)/(:any)/settings/members/afacilitator/(:num)']	= 'connect/member_settings_make_apprentice_facilitator/$2/$3';
$route['(groups|locations|ministries)/(:any)/settings/pages']						= 'connect/page_settings/$2';
$route['(groups|locations|ministries)/(:any)/settings/pages/add']					= 'connect/add_page_settings/$2';
$route['(groups|locations|ministries)/(:any)/settings/pages/(:num)']				= 'connect/edit_page_settings/$2/$3';
$route['(groups|locations|ministries)/(:any)/settings/pages/(:num)/delete']			= 'connect/delete_page_settings/$2/$3';
$route['(groups|locations|ministries)/(:any)/(:any)']								= 'connect/show_page/$2/$3';
$route['(groups|locations|ministries)/(:any)']										= 'connect/show/$2';
$route['(groups|locations|ministries)']												= 'connect/finder';

$route['generocity/give']								= 'givingback';
$route['give']											= 'givingback';
$route['givingback/process']							= 'givingback/process';

$route['images/square/(:num)/(:any)/(:any)']			= 'image_manip/square/$1/$2/$3';
$route['images/golden/(:num)/(:any)/(:any)']			= 'image_manip/golden/$1/$2/$3';
$route['images/golden_height/(:num)/(:any)/(:any)']		= 'image_manip/golden_height/$1/$2/$3';
$route['images/golden_tall/(:num)/(:any)/(:any)']		= 'image_manip/golden_tall/$1/$2/$3';
$route['images/rect/(:num)/(:num)/(:any)/(:any)'] 		= 'image_manip/rect/$1/$2/$3/$4';

$route['admin']											= 'admin';
$route['admin/video_list']								= 'admin/video_list';
$route['admin/services']								= 'admin/services';
$route['admin/service/(:num)']							= 'admin/edit_service/$1';
$route['admin/settings']								= 'admin/settings';
$route['admin/schedule']								= 'admin/schedule';
$route['admin/schedule/add']							= 'admin/add_schedule';
$route['admin/schedule/edit/(:num)']					= 'admin/edit_schedule/$1';
$route['admin/groups']									= 'admin/groups';
$route['admin/groups/add']								= 'admin/add_group';
$route['admin/groups/view/(:num)']						= 'admin/view_group/$1';
$route['admin/groups/view/(:num)/:any']					= 'admin/view_group/$1';
$route['admin/groups/edit/(:num)']						= 'admin/edit_group/$1';
$route['admin/big_idea']								= 'admin/big_idea';
$route['admin/big_idea/add']							= 'admin/add_big_idea';
$route['admin/big_idea/edit/(:num)']					= 'admin/edit_big_idea/$1';

//pages
//$route['locations/(:any)']							= 'pages/locations/$1';

$route['watch']											= 'watch';

$route['admin/users']									= 'admin/users';
$route['admin/users/add']								= 'admin/add_user';
$route['admin/users/edit/(:num)']						= 'admin/edit_user/$1';

$route['account']										= 'account';
$route['account/:any']									= 'account';
$route['register']										= 'account/register';
$route['reset']											= 'account/reset';

$route['twitter']										= 'twitter';
$route['twitter/authenticate']							= 'twitter/authenticate';
$route['twitter/authenticated']							= 'twitter/authenticated';
$route['twitter/tweet']									= 'twitter/tweet';

$route['(:any)']										= 'connect/show_page/news/$1';


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */