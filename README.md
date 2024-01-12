## This readme is still a work in progress, as i am rather new to all this, and more focussed on the project itself

Collection Tracker:
  Small web-app designed to keep track of several types of collections.
  In co-operation with 'Aletho: ICT Dagbesteding', i started writing this App recently, to get a better grasp on the workflow for web development.
  This repository started around v1.1, where i had already re-worked the majority of the code base, based on feedback of the version 1.0 test i had released.
  Meaning that the entire planning phase, and feedback loop to draft a working concept version, was left out.

  The goal of this project, is for me to learn more about the general workflow, that is closer to what is used by companies.
  I have mostly focussed on the back-end so far, so i often make things that are not very user friendly, and for example has lots of page-reloads to get things done.
  I order to be a more complete back-end developer, i need to know more about how the front-end design affects the back-end code design.
  Often things end up quite different, when you start working with JS fetch requests to update information displayed on the webpage.

Current Features:
  - Build in Administrator account.
  - Register feature for regular users only.
  - Password reset for admins only.
  - Create Series to add Albums to.
  - Create Albums to add to Series.
  - Search function for users, to reduce the need to scroll big series.
  - A toggle based collection, meaning you select a serie, and then simply toggle what you own or remove what you do not.

Notable Technical Notes:
  - Overly simple account management, meaning no tokens\e-mail to register\reset user details.
  - No browser cookies to store data required in the browser (excl the default session cookie).

Visual Examples of what the App looks like currently:
![Preview](https://github.com/Lordbufu/CollectionTracker/assets/19768243/3b6feae5-a5d9-4e58-888a-95aea0d0ba6d)
![Preview3](https://github.com/Lordbufu/CollectionTracker/assets/19768243/08566c27-c420-42dc-9230-8ff3d5bc466f)
![Preview2](https://github.com/Lordbufu/CollectionTracker/assets/19768243/e7b21f20-8f13-41f3-a1cd-6c37480274c7)
![Preview4](https://github.com/Lordbufu/CollectionTracker/assets/19768243/6fab3805-8b8d-4dad-abc5-e798bb7b91e8)


What do i need to setup\know to host this ?:
  Somekind of web hosting App/Service, that matches the required tool, and the knowledge to opperate/config that.
  
What is required for this to work ?:
  PhP 8.2.6 (or higher)
  MariaDb - cp1252 West European (latin1)

Do i have to edit/config anything to make it work ?:
  - '\config.php' will need to be edited with the connection details, as the settings in there are from a local testing enviroment.
  - The same file also needs to be edited to change the error reporting mode, as its also set to testing/development mode.






