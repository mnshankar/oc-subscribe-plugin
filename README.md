##Subscribe plugin
Easily add a "Subscribe" button on your website.

This plugin hooks into Rainlab Blog events and allows users to receive emails when a blog gets published

###Main features
* Sends an email to all subscribed users using Mail::queue
* Allows for easy unsubscription
* Emails are sent out only when the status of the blog is set to published.
 
###Components
* Signup form - This allows users to enter their email address and either click on the "Subscribe" or "Unsubscribe" button.

###Mail specific information
* Be sure to setup your email provider as per the documentation
* Setting up a queue driver will result in substantial speedup especially if you have a large number of subscribers 

###Dependencies
* RainLab Blog

###Installation
1. Go to __Settings > "Updates & Plugins"__ page in the Backend.
2. Click on the __"Install plugins"__ option.
3. Type __Subscribe__ text in the search field, and pick the appropriate plugin.
4. To add the component on your front-end page :
```
{% component 'Signup' %}
```
