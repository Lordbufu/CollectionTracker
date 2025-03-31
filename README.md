# Collection Tracker

This project was based on the following laracast learning resources:

Outdate Practitioner Course:

[PHP practitioner](https://laracasts.com/series/php-for-beginners) / [Source code](https://github.com/laracasts/The-PHP-Practitioner-Full-Source-Code)

Current PHP for Beginners:

[PHP For Beginners](https://laracasts.com/series/php-for-beginners-2023-edition) / [Source code](https://github.com/laracasts/PHP-For-Beginners-Series)

## Short introduction:
This project was started to give me a better idea of what backend webdevelopment is all about, and how a project looks like in a work enviroment.
It also was as a good way to see where i can improve myself, and what my strength\weaknesses are in terms of backend development.
Like mixing dutch and english in the code, turned out to be a very bad idea, and something i would do very different on a new project.
But also the mess of classes that i made, that was more of a personal experiment then anything else, it all works as intended but in some cases not realy a good way of doing things.

I am also not a designer, so a the overall design and look/feel is likely to be lacking quite a bit.
And because its my first full project, some it looks very much tagged on after the design phase, simply because it likely was.

This is only here for portfolio/review reasons, there wont be a installation guide.
And there really isnt a need either imo, its all fairly straight forward, if you follow the default software resources.
The 2 important things one should consider/do, have been listed below the requirements.

## Requirements to run this project:
* XAMPP or a other hosting solution:
	- Appache.
	- PhP (v8.2.12 or higher).
* A MYSQL compatible database.
	- MariaDB (used for development):
	- charset: UTF-8 Unicode (utf8mb4) 
	- collation: latin1_swedish_ci
* Composer.

The database details in 'config.php', should be updated to what ever your enviroment settings are.

And before hosting the website, the following composer command should be executed from the projects root:

	'composer dump-autoload'

A few images to show how the project looks like when its live, and has a populated database:
