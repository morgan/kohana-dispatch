# Getting Started

Dispatch is intended to be the "query builder" for REST services in Kohana.

## Basic Usage

### Find

	// User resource
	$user = Dispatch::factory('users/1');
	
	$result = $user->find();
	
	var_dump($result->loaded());

### Iterate collection

	foreach (Dispatch::factory('users')->find() as $user)
	{
		echo $user['name'];
	}

### Create

	// Generate user resource
	$user = Dispatch::factory('users');
	
	// Set POST data
	$user->set('name', 'Micheal Morgan');
	
	// Process and retrieve result
	$result = $user->create();
	
### Update

	$user = Dispatch::factory('users/1');
	
	$user->set('active', 1);
	
	$result = $user->update();
	
### Delete
	
	$user = Dispatch::factory('users/1');
	
	$result = $user->delete();
