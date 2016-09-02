##Subscribe plugin
It turns out, sending an email to a group of subscribers is actually quite a hairy problem. One would 
think storing a set of emails, looping over each one of them and sending out emails is all that needs to be done.
But this solution only works for a very small subscriber base(5-10 max). 
Any more, and you could be invoking the wrath of the email Gods - your emails could be classified as spam 
or worse, your domain could potentially be flagged as a spammer!
(Tag v1.0 of [this project](https://github.com/mnshankar/oc-subscribe-plugin) uses the naive approach.. Feel free to take a look if you are interested).

The solution is to use the services of a third-party provider like [MailChimp](http://mailchimp.com/) 
that specializes in safe email transmission, and handles all the myriad requirements of bulk mailing.

This OctoberCMS plugin allows you to easily add a "Subscribe" button on your website. These subscribers get
 added to a "list" on Mailchimp.
The plugin also hooks into Rainlab Blog events, and allows subscribers to receive emails 
when a blog gets published (via Mailchimp Campaigns - Each blog publish event results in the creation of a campaign)

###Main features
* Uses Mailchimp api V2.0
* Clean way to send subscribers an email using MailChimp
* unsubscription is easily handled by Mailchimp
* Emails are sent out only when the status of the blog is set to published (on create or update)
 
###Components
* Signup form - This allows users to enter their email address and click on the "Subscribe" button.

###Dependencies
* RainLab Blog

###Installation
1. Go to __Settings > "Updates & Plugins"__ page in the Backend.
2. Click on the __"Install plugins"__ option.
3. Type __Subscribe__ text in the search field, and pick the appropriate plugin.
4. Make sure you enter your sender name and sender email under Settings->Mail Configuration
5. To add the component on your front-end page/layout :
```
{% component 'Signup' %}
```
6. After logging into Mailchimp, create a list for holding subscriber emails.
7. On your backend -> Settings, under the "Blog" category, an option for "Mailchimp Subscription" becomes available. Please enter 
your Mailchimp API key and List ID in the spaces provided.

##NOTE:
if you want to use the component on a partial, drag the component onto your layout and move the code {% component 'Signup' %} onto the partial. This way, the ajax framework will be able to find the required handlers.