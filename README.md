# EE-418---Moodle-Open-Source
To set up the assignment planner feature, first the user must edit the theme settings in moodle to add the page to the dropdown.
Navigate to "Site Administration" -> "Appearance" -> "Theme Settings", then scroll down to the box labeled "User Menu Items". On the last line, put the following:
Assignments|/planner/index.php

The database will also need some tinkering. The code uses a new user with the username of "moodleaccess" and the password of "root". Either create a user on the moodle
mysql database with these credentials, or alter the relevant lines in outputcomponents.php to match credentials of another user with access.
