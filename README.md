# Anti Spam Questions - A [Nova](https://anodyne-productions.com/nova) Extension
## Created for you by [Sim Central](https://simcentral.org)

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-anti-spam-questions/releases/tag/v1.2.0"><img src="https://img.shields.io/badge/Version-v1.2.0-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.7.19+-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v8.x-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension allows the Game Manager to add questions to the Contact and Join forms for spambot prevention. A question will be chosen at random on page load and displayed to the user.

This extension requires:

- Nova 2.7.19+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)

## Upgrade Considerations

### Upgrading from a version older than 1.2.0
The controller code injected by older releases of this extension didn't carry version markers. After upgrading the extension files, open the admin Status panel - it will detect the existing `contact()` / `join()` methods in `application/controllers/Main.php` and offer an **Update Controller Code** button to replace them in place with the new shim form. No manual surgery required.

If anything looks off, the fallback is always to replace `application/controllers/Main.php` with the stock Nova stub, then click **Install Controller Code** on the admin page.

### Upgrading Nova
- If upgrading Nova with this Nova Extension already deployed:
- Remove `$config['extensions']['enabled'][] = 'nova_ext_anti_spam_questions';` from `application/config/extensions.php` prior to the Nova upgrade.
- After upgrading Nova to 2.7.19+, follow the installation steps below. The database rows still contain your questions.

## Installation

- Install Required Extensions.
- Copy the entire directory into `application/extensions/nova_ext_anti_spam_questions`.
- Add the following to `application/config/extensions.php` - be sure the `jquery` line appears before `nova_ext_anti_spam_questions`:
```
$config['extensions']['enabled'][] = 'nova_ext_anti_spam_questions';
```

### Setup Using Admin Panel

- Navigate to your Admin Control Panel.
- Choose **Anti Spam Questions** under Manage Extensions.
- The **Status** panel at the top shows the live state of the contact/join controller code.
- Click **Install Controller Code** to inject the anti-spam shims into `application/controllers/Main.php` so the contact and join forms verify the security answer before processing.

Installation is complete when the Status panel reads "Installed and up to date".

## Usage

- Navigate to your Admin Control Panel.
- Choose **Anti Spam Questions** under Manage Extensions.
- Add, edit, or remove questions. Each question can have multiple acceptable answers - click **+ Add Row** to add another acceptable answer, and **Remove Row** to drop one.
- A random question is shown on the contact and join forms; the user has to enter one of the acceptable answers before the form can be submitted.
- Answers are NOT case sensitive, but remember to include all acceptable forms:
	- Starship, USS Starship, U.S.S. Starship
	- Seventeen, 17

## Issues

If you encounter a bug or have a feature request, please report it on GitHub in the issue tracker here: https://github.com/reecesavage/nova-ext-anti-spam-questions/issues

## License

Copyright (c) 2026 Reece Savage.

This module is open-source software licensed under the **MIT License**. The full text of the license may be found in the `LICENSE` file.
