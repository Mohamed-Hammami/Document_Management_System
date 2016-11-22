Document Management System
==========================

A web application that manages, tracks, and store documents online, it also keeps record of different files versions

Used Bundles
--------------

Bundles used in this application

  * Symfony 2.8

  * FOSUserBundle

  * VichUploaderBundle

  * AvanzuAdminThemeBundle

  * AsseticBundle.
  
  * FOSJsRoutingBundle.
  
  * TetranzSelect2EntityBundle
  
  * Bootstrap & Jquery in the view part
  
  * DoctrineFixturesBundle

Implemented Features
--------------

  * Tree structured folder and files
  
  * Several versions per file
  
  * Copy and paste for files and folder
  
  * User rights managements for files
  
  * Lock/Unlock feature for files
  
  * Search by name, user, & tag
  
  * User & user's group managment
  
  * Mailing et notification features
  
Installation & Configuration
----------------------------

    
You have to load some fixtures before the first use
    
    php app/console doctrine:fixtures:load
    
Customize the paramters in `parameters.yml`:

*  mailer_user: server email address 
*  mailer_password: server email password
*  upload_path: the files upload path *must be outside web folder*
*  avatar_path: the users' avatars *must be inside web folder*
    
    ```
    # parameters.yml
    parameters:
        database_host: 127.0.0.1
        database_port: null
        database_name: DMS_DB
        database_user: root
        database_password: null
    
        mailer_transport: smtp
        mailer_encryption: ssl
        mailer_auth_mode:  login
        mailer_host: smtp.gmail.com
        mailer_user: ****************
        mailer_password: ****************
    
        secret: ThisTokenIsNotSoSecretChangeIt
    
        upload_path: ****************
        avatar_path: ****************
        skin: skin-blue
    ```
    

Screenshots
-----------

![login](https://cloud.githubusercontent.com/assets/19515339/20526696/77e9e2d0-b0c5-11e6-8b26-726418282f12.PNG)


Authors
--------------  

* **Mohamed Hammami** - *Student in IT engineering & Junior Web developper* - 
* **Email** - *mohamed.hammami.112358@gmail.com* - 

Enjoy!

