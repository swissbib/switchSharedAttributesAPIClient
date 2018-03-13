# switchSharedAttributesAPIClient
Independent Component. Client for the [Switch Edu-ID Shared Attributes API](https://forge.switch.ch/projects/edu-id/wiki/Swiss_edu-ID_shared_attributes). Used in [Swissbib/Vufind](https://github.com/swissbib/vufind) and in [Pura Back-End](https://github.com/swissbib/pura-backend).

## Usage

```php
use SwitchSharedAttributesAPIClient\SwitchSharedAttributesAPIClient;

$config['auth_user'] = 'username';
$config['auth_password'] = 'password';
$config['base_endpoint_url'] = 'https://test.eduid.ch/sg/index.php'; // or https://eduid.ch/sg/index.php

$switchApi = new SwitchSharedAttributesAPIClient($config);

$userEduId = '1234567890@eduid.ch';

$switchGroupId = 'a4c40594-6d7d-41bc-9fb2-7239d2fcf892';

//add the user to the group
$switchApi->activatePublisherForUser($userEduId, $switchGroupId);
```

## Installation via composer

Add this to your `composer.json` :
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:swissbib/switchSharedAttributesAPIClient.git"
        }
    ],
    "require": {
        "swissbib/switchSharedAttributesAPIClient": "dev-master"
    }
}
```

And do `composer update`.