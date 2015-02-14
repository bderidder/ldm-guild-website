<?php

namespace LaDanse\ServicesBundle\EventListener;

class Features
{
    const CALENDAR_VIEW = 'Calendar.View';
    const CALENDAR_ICAL = 'Calendar.iCal';

    const EVENT_VIEW    = 'Event.View';
    const EVENT_EDIT    = 'Event.Edit';
    const EVENT_DELETE  = 'Event.Delete';
    const EVENT_CREATE  = 'Event.Create';
    const EVENT_LIST    = 'Event.List';

    const SIGNUP_CREATE = 'Signup.Create';
    const SIGNUP_EDIT   = 'Signup.Edit';
    const SIGNUP_DELETE = 'Signup.Delete';

    const CLAIMS_LIST   = 'Claims.List';

    const ABOUT_VIEW    = 'About.View';

    const GALLERY_VIEW  = 'Gallery.View';

    const CLAIM_CREATE  = 'Claim.Create';
    const CLAIM_EDIT    = 'Claim.Edit';
    const CLAIM_REMOVE  = 'Claim.Remove';
    const CLAIM_VIEW    = 'Claim.View';

    const FORUM_VIEW    = 'Forum.View';

    const HELP_VIEW     = 'Help.View';

    const MENU_VIEW     = 'Menu.View';

    const PRIVACY_VIEW  = 'Privacy.View';

    const REGISTRATION_CREATE = 'Registration.Create';

    const SETTINGS_VIEW             = 'Settings.View';
    const SETTINGS_PROFILE_VIEW     = 'Settings.Profile.View';
    const SETTINGS_PROFILE_UPDATE   = 'Settings.Profile.Update';
    const SETTINGS_PASSWORD_VIEW    = 'Settings.Password.View';
    const SETTINGS_PASSWORD_UPDATE  = 'Settings.Password.Update';
    const SETTINGS_CALEXPORT_VIEW   = 'Settings.CalExport.View';
    const SETTINGS_CALEXPORT_UPDATE = 'Settings.CalExport.Update';
    const SETTINGS_CALEXPORT_RESET  = 'Settings.CalExport.Reset';
    const SETTINGS_NOTIF_UPDATE     = 'Settings.Notifications.Update';

    const TEAMSPEAK_VIEW = 'TeamSpeak.View';

    const FEEDBACK_VIEW  = 'Feedback.View';
    const FEEDBACK_POST  = 'Feedback.Post';
}