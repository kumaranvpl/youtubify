
[Source](https://raw.githubusercontent.com/kumaranvpl/youtubify/master/documentation/documentation.html "Permalink to Youtubify")

# Youtubify

## Creating a new database

First thing you want to do before installing **Youtubify** is to create a new database on your mysql server. If you already know how to do this/or have already created one just skip to the next step.

Your host will most likely be running **phpMyAdmin** as mysql manager, if that's the case here's a step by step guide (if not the proccess will be very similar on other managers).

Login to your control panel, find and click phpMyAdmin link: ![][1] Click on the database tab in the top menu, enter any name you like and click create.![][2]

## Uploading Files

After creating a database, unzpip the .zip file you donwloaded and upload the **contents** of **Youtubify** folder to your servers root folder (usually called www or html or something similar) or a sub-directory, shared hosting providers usually have a web based file manager, but you should use something like [Filezilla][3] to do the upload as the web based managers can cause various problems fairly often.

Make sure that **application/storage** and all the **sub-folders** are writable by your server (have 777 permissions if you are on shared hosting). You can change files and folders permissions by right-clicking them in the filemanager, clicking file permissions, and then entering 777 in the permissions field.

## Installing Youtubify

After you uploaded Youtubify files, simply open up your site url and follow on-screen instructions to finish the installation.

![][4]

## Updating **Youtubify** to new versions
* 1\. Extract the .zip file you downloaded.
* 2\. Upload and overwrite all the files same way you did when you installed Youtubify.
* 3\. Visit http://yoursite.com/update url and click **Update Now** button.

## API Keys

In order make some features of the **youtubify** work you will need to register for a some API Keys. Check out the sections below for information on how to get them. Once you've got your keys simply enter them in corresponding fields in admin area -&gt; third party services and keys.

![][5]

## Youtube API Key

This key is **required** in order to play music on youtubify. You can use the same **google app** you have created for social login and google anlytics (refer to social login google section of the docs). Simply create a new api key from your google project page and enable youtube api from the same page. **Note: leave refferers field empty**. Then enter this key in admin area &gt; settings &gt; 3rd party keys &gt; Youtube API Key.

![][6]![][7]![][8]

## Echonest API Key

This key is **optional.** When entered it will enable some extra features like artist radio, biography and images. To get echonest api key, register for an account here https://developer.echonest.com/account/register, once you have created an activated our account you will be able to see your api key here https://developer.echonest.com/account/profile, simply enter it in admin area &gt; settings &gt; 3rd party keys &gt; Echonest API Key.

![][9]

## Spotify Key and Secret

Spotify keys are **optional.** When entered it will enable new releases fetching directly from spotify. To get these keys, login or register [here][10], accept all the terms, then create a new application at this [url][11] (you can enter anything in name and description fields), then you will be able to see your keys, simply enter them in admin area &gt; settings &gt; 3rd party keys &gt; Spotify ID and Spotify Secret fields.

![][12]

## Social Login

In order make social login (logging in via google, twitter and facebook) work you will need to register for a corresponding app and get that services **Client ID** and **Client Secret**. Check out the section for each service for a detailed instruction for how to register and get those tokens. Once you've got your client id and secret simply enter them in corresponding field in admin area -&gt; third party services and keys.

![][13]

## Facebook

You can resgister for a facebook app [Here][14] by clicking on **My Apps &gt; add a new app** at the top. Once you create the app you can find your id and secret in your app dashboard, you will also need to enter your site url in **Settings &gt; site url field** and make your app public from **Staus &amp; Review** tab.

![][15]![][16]

## Twitter

You can register for a twitter app [Here][17], after you register you will need to enter your site url in **Website** and **Callback Url** fields in your app settings, you can find your key and secret in the **Keys &amp; Access Tokens** tab.

![][18]![][19]

## Google

You can register for a google app [Here][20] by clicking on **Create new project**, after creating new project go to credentials tab and in the authorized redirect uri field enter **http://yoursiteurl.com/auth/social/google/login**, you might also need to enable **Google plus** and **contacts** APIs.

![][21]![][22]

## Mail

In order to make mail work (for password reset and other functions), you will need to register for a mandrill API key. Mandrill is used to avoid the many problems that occur when using public smtp servers (like gmail or your hostings private one). Here's how to get your API key.

If you want to use SMTP instead (or one of the other available options), go to **admin area &gt; settings &gt; mail** and simply change mandrill to another option and enter any other required details like host, usernamer, password etc.

### Register for mandrill api key

To register for mandrill key go to this [url][23], create a new account and then log in. Then click **get api keys -&gt; +add api key**

![][24]![][25]

Then simply copy mandrill api key and enter it into corresponding field in **admin area &gt; settings &gt; mail**

![][26]![][27]

## Analytics

### Registering for google analytics account

Analytics page is powered by google analytics so you will need to register for google analytics and add their supplied code to your site. Here's how to do it.

* 1\. Go to this [url][28]. You will be prompted to login to your google account or create a new one if you don't have it already. Do it.
* 2\. Click on Admin -&gt; Property -&gt; Create new Property -&gt; fill out required fields -&gt; click on get tracking ID ![][29]![][30]
* 3\. Go to **Admin area -&gt; settings -&gt; third party service keys** and paste the code into **google analytics tracking code** field (only paste in the code that starts with UA, not the whole script). ![][31]

### Registering for google ID

You will also need to enter your **google ID** in the same page for analytics to work. It's the same ID as for google social login so refer to documenation on social login to see how to get that key. Only make sure that enable google analytics API from google developers console and enter your site url in javascript origins field.

![][32]![][33]

### Viewing analytics information

Once you have created analytics account and entered it in settings page, go to admin area -&gt; analytics, click on **Access google analytics button**, then click **accept**

![][34]

And you are done. You can now easily view analytics information right from the admin area. You can also select different analytics account, property and view from the bottom of the page.

![][35]

## Appearence

From appearence page you can modify how your site looks by changing colors, fonts, sizes and more. Simply change the value in the field you want and click **save** on the right. Youtubify will compile a custom css stylesheet (just as if it was written by a programmer!) without you needing to have any coding knowledge. Original stylesheet will be preserved so you can switch back at any time. You can also create multiple stylesheets(light, dark etc) and switch them at any time from panel on the left. You can also insert custom css, by typing it into custom css panel.

Note that **variables** and **functions** might sometimes be used in some of the appearence fields. These are very simple to understand. For example, by default **main background color** is **lighten($main-color, 5%)**, this simply means that it will take the color value from $main-color field in appearence page (#19191b by default) and make that color 5% lighter.

![][36]

## Translations

You can translate youtubify right from the admin area so there's no need to mess with configuration files or 3rd party applications. Simpley open translations page and enter your translation for a particular line under **translation** column in the table.

Note that while you can translate the original (englsish) locale, it is recommended to create a new one (frome the panel on the left) so you don't need to worry about overwriting it with future updates.

![][37]

## Users

From users page, you can get an overview of your site users, create a new one, delete existing ones and edit their profile information.

![][38]

## Settings

On the settings page you can change various options on your site from your homepage view to user registration. There's a description under each options so you shouldn't have any troubles figuring out which setting does what.

![][39]

## Support

If you have any problems or questions, please contact me here <https: github.com="" kumaranvpl=""> to send us a message instead of doing it in the comments, so we can resolive it as fast as possible, thanks!

[1]: https://raw.githubusercontent.com/assets/images/db_ex_2.png
[2]: https://raw.githubusercontent.com/assets/images/db_ex_3.png
[3]: https://filezilla-project.org/
[4]: https://raw.githubusercontent.com/assets/images/install_ex_1.png
[5]: https://raw.githubusercontent.com/assets/images/api_keys_ex_1.png
[6]: https://raw.githubusercontent.com/assets/images/youtube_key_ex_1.png
[7]: https://raw.githubusercontent.com/assets/images/youtube_key_ex_2.png
[8]: https://raw.githubusercontent.com/assets/images/youtube_key_ex_3.png
[9]: https://raw.githubusercontent.com/assets/images/echonest_key_ex_1.png
[10]: https://developer.spotify.com/my-applications/#!/register
[11]: https://developer.spotify.com/my-applications/#!/applications/create
[12]: https://raw.githubusercontent.com/assets/images/spotify_ex_1.png
[13]: https://raw.githubusercontent.com/assets/images/social_ex_5.png
[14]: https://developers.facebook.com/
[15]: https://raw.githubusercontent.com/assets/images/facebook_ex_1.png
[16]: https://raw.githubusercontent.com/assets/images/social_ex_1.png
[17]: https://dev.twitter.com/apps/new
[18]: https://raw.githubusercontent.com/assets/images/twitter_ex_1.png
[19]: https://raw.githubusercontent.com/assets/images/social_ex_2.png
[20]: https://cloud.google.com/console/project
[21]: https://raw.githubusercontent.com/assets/images/google_ex_1.png
[22]: https://raw.githubusercontent.com/assets/images/social_ex_3.png
[23]: https://mandrill.com/signup/
[24]: https://raw.githubusercontent.com/assets/images/mandrill_ex_1.png
[25]: https://raw.githubusercontent.com/assets/images/mandrill_ex_2.png
[26]: https://raw.githubusercontent.com/assets/images/mandrill_ex_3.png
[27]: https://raw.githubusercontent.com/assets/images/mandrill_ex_4.png
[28]: https://www.google.com/analytics/web/
[29]: https://raw.githubusercontent.com/assets/images/analytics_ex_1.png
[30]: https://raw.githubusercontent.com/assets/images/analytics_ex_2.png
[31]: https://raw.githubusercontent.com/assets/images/analytics_ex_3.png
[32]: https://raw.githubusercontent.com/assets/images/analytics_ex_6.png
[33]: https://raw.githubusercontent.com/assets/images/analytics_ex_7.png
[34]: https://raw.githubusercontent.com/assets/images/analytics_ex_5.png
[35]: https://raw.githubusercontent.com/assets/images/analytics_ex_4.png
[36]: https://raw.githubusercontent.com/assets/images/appearence_ex_1.png
[37]: https://raw.githubusercontent.com/assets/images/translations_ex_1.png
[38]: https://raw.githubusercontent.com/assets/images/users_ex_1.png
[39]: https://raw.githubusercontent.com/assets/images/settings_ex_1.png
  </https:>