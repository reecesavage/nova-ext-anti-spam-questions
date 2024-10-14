# Anti Spam Questions - A [Nova](https://anodyne-productions.com/nova) Extension
## Created for you by [Sim Central](https://simcentral.org)

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-anti-spam-questions/releases/tag/v1.1.1"><img src="https://img.shields.io/badge/Version-v1.1.0-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.7.12+-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v8.x-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension allows the Game Manager to add questions to the Contact and Join forms for spambot prevention. A question will be chosen at random on page load and displayed to the user.

This extension requires:

- Nova 2.7.12+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)

## Upgrade Considerations
- If upgrading Nova 2.6+ with this Nove Extension already deployed:
- Remove `$config['extensions']['enabled'][] = 'nova_ext_anti_spam_questions';` from `application/config/extensions.php` prior to the Nova upgrade.
- After upgrading Nova to 2.7.5+, follow the installation steps below. The database tables still contain your data

## Installation

- Install Required Extensions.
- Copy the entire directory into `applications/extensions/nova_ext_anti_spam_questions`.
- Add the following to `application/config/extensions.php`: - Be sure the `jquery` line appears before `nova_ext_anti_spam_questions`
```
$config['extensions']['enabled'][] = 'nova_ext_anti_spam_questions';
```
### Setup Using Admin Panel

- Navigate to your Admin Control Panel
- Choose Anti Spam Questions under Manage Extensions
- Click Update Controller Information to add the `contact` and `join` functions to your `application/controllers/main.php` file.

Installation is now complete!

## Usage

- Navigate to your Admin Control Panel
- Choose Anti Spam Questions under Manage Extensions
- Add, Remove, or Edit Quesions.
- Answers are NOT case Sensitive, but remember to add all acceptable answers:
	- Starship, USS Starship, U.S.S. Starship
	- Seventeen, 17

## Issues

If you encounter a bug or have a feature request, please report it on GitHub in the issue tracker here: https://github.com/reecesavage/nova-ext-anti-spam-questions/issues

## License

Copyright (c) 2024 Reece Savage.

This module is open-source software licensed under the **MIT License**. The full text of the license may be found in the `LICENSE` file.
