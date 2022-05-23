# Collection of rewrites

## Authentications and handling user related to a website-gallery

Majority of files are uncommented and will be added at some point. This project aims to develop and experiment with how to handle authentication of logged in users. Mainly php is being used. However partially css, html and js may be used to give more feel to how a website gallery can look. And how php functions can be used.

## Browsing the repo:

- [re-re](./re-re/) Is the most newly and nuanced with methods to handle users. File structure is quiet simple when you have understanding of it. [re-re/classes](./re-re/classes) Contains all definitions for classes, mainly [ReAuthentication](re-re/classes/auth.cls.php), usually referenced to with $auth. Handles all user actions when it comes to registration, login and logout. Not all methods are defined, as for being admin (is_admin) and banning user (is_banned) are just placeholders for values that can be set in the future. And actions that these users can do.

- [re](./re) Is empty, because all files where moved to [re-re](./re-re) Instead of being copied, these can probably be found through the repository commit history (maybe).

- [php](./php) "Oldest" folder most of the rewrite is based from this folder. In return [php](./php) is based on another repo: [Re-Branded-Gallery](https://github.com/Slayga/Re-Branded-Gallery/tree/master/php)

- [index](index.php) Was used to test [php](./php) functions. And also used to plan what was going to be implemented, those ideas are out of date now as the time frame to complete them where just to tight. And the constant rewrites did nothing better for that time frame.

## Reflection

This repo's "cause"/"mission" was to develop better ways to communication between client and database. It has been difficult, as constant rewrites to improve systems tightened the time frame to completion. But over those issues, it has been a valuable learning lessons on how to authenticate a user and how to make easy to read code. Also oop is with no doubt easier for these problems than procedural code.