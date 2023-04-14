=== Bonaire ===
Author: Demis Patti
Tags: Contact Form 7, Flamingo, Message, Reply, Email, E-Mail, Send, Send Mail, Send E-Mail
Requires at least: 5.5
Tested up to: 6.1.1
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XLMMS7C62S76Q

== Description ==

Send replies to messages you received trough a default [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) contact form and you store with [Flamingo](https://wordpress.org/plugins/flamingo/).

== Features ==

- Send replies to messages received trough a default "Contact Form 7" contact form
- Store replies on your mail server's 'Sent Items' folder
- Dashboard Widget lists incoming messages
- Email replies are text-only

== Requirements ==
- Familiarity with configuration and usage of both Contact Form 7 and Flamingo
- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) installed and activated
- [Flamingo](https://wordpress.org/plugins/flamingo/) installed and activated
- PHP IMAP extension installed and enabled on your web server
- PHP 5.6+

PHP version 5.6 or above.

== Installation ==

1. Upload the `bonaire` folder to your `/wp-content/plugins/` directory.
2. Activate the "Bonaire" plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==

= Where do I interact with this plugin and how does it work? =

Please visit the plugin help tab for further information.

= Why doesn't it show up in the settings menu after installing and activating? =

Most likely, this is because you didn't install and activate the plugins Bonaire was made for, namely 'Contact Form 7' and 'Flamingo'.
After installing and activating these two plugins, Bonaire will show up on in the settings menu.

= I have the two plugins installed and activated but it doesn't work! =

That's unlikely.
First of all, you have to enter the email account settings for the email account that you use on the contact form. You can do that on the settings page "Settings -> Bonaire".
If you want to store your replies in your mail server's "Sent Items" folder, you have to enter your IMAP settings, too.

= Are there any known limitations? =

- Handling attachments is not supported
- Simple plain text email format for now (no html format yet)
- Supports one contact form for now

= Can you help me? =

Yes. I have a look at the plugin's support page once or twice a month and I provide some basic support there.

= Are there any known issues? =

No.

== Limitations ==
- Not yet compatible with PHP version 8 and above

== Screenshots ==

1. Settings Page
2. Dashboard Widget
3. Reply Form

== Changelog ==

= Version 1.0.0 =
1. First commit
