$router->get('/home/terrains', 'HomeController@terrains');
$router->get('/home/tournois', 'HomeController@tournois');


$router->get('/gestionnaire/dashboard', 'GestionnaireController@dashboard', 'gestionnaire.dashboard');
$router->get('/dashboard/gestionnaire', 'GestionnaireController@dashboard', 'gestionnaire.dashboard.alt');