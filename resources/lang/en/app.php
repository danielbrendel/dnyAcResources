<?php

/*
    AC-Resources (dnyAcResources) developed by Daniel Brendel

    (C) 2022 by Daniel Brendel

    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

return [
    'select_type' => 'Type',
    'select_sorting' => 'Sorting',
    'search_item' => 'Text search...',
    'login' => 'Login',
    'register' => 'Register',
    'register_username' => 'Username',
    'register_email' => 'E-Mail',
    'register_password' => 'Password',
    'register_password_confirmation' => 'Password confirmation',
    'banner_title' => env('APP_BANNER_TITLE', 'AC-Resources'),
    'banner_slogan' => env('APP_BANNER_SLOGAN', 'Your portal to AssaultCube community resources'),
    'cancel' => 'Cancel',
    'email' => 'E-Mail',
    'close' => 'Close',
    'password' => 'Password',
    'no_account_yet' => 'No account yet? Sign up!',
    'recover_password' => 'Recover password',
    'login_successful' => 'Login successful',
    'invalid_credentials' => 'Invalid credentials',
    'user_not_found' => 'User not found',
    'user_not_valid' => 'User not  valid',
    'register_already_signed_in' => 'You are already logged in with an account',
    'register_captcha_invalid' => 'Please specify a valid captcha value',
    'register_email_in_use' => 'The specified E-Mail address is already in use',
    'register_username_in_use' => 'The specified username is already in use',
    'register_username_invalid_chars' => 'Invalid characters. Your name must contain at least one character (a-z, lowercase) and may contain numbers and the characters \'-\' and \'_\'.',
    'mail_subject_register' => 'Your registration',
    'mail_password_reset_subject' => 'Password reset',
    'password_mismatch' => 'The passwords do not match',
    'hash_not_found' => 'Hash not found',
    'mail_email_changed_title' => 'Your E-Mail address has been changed',
    'mail_salutation' => 'Dear :name',
    'mail_email_changed_body' => 'Your E-Mail address has been successfully changed. Your new E-Mail address: :email',
    'mail_pw_changed_title' => 'Your password has been changed',
    'mail_pw_changed_body' => 'Your password has been successfully changed',
    'mail_password_reset_title' => 'Password reset',
    'mail_password_reset_body' => 'A password reset request has been performed. Click the link to reset your password. If you did not request this process you can simply ignore this e-mail.',
    'mail_password_reset' => 'Reset',
    'mail_registered_title' => 'Your registration',
    'mail_registered_body' => 'Your account has been successfully created. Before you can login you need to confirm your e-mail address. Therefore click the link below.',
    'mail_registered_confirm' => 'Confirm',
    'password_changed' => 'Password changed',
    'email_changed' => 'E-Mail address changed',
    'logout_successful' => 'You have been successfully logged out',
    'register_confirmed_ok' => 'Your account has been confirmed. Have fun using our service!',
    'register_confirm_email' => 'Your account has been created! Please confirm your E-Mail address before logging in. <a href="' . url('/resend') . '/:id">Resend confirmation link</a>',
    'resend_ok' => 'Confirmation e-mail was resent. <a href="' . url('/resend') . '/:id">Try again</a>.',
    'password_reset_ok' => 'The password reset operation has been processed successfully. You can now login with your new password.',
    'pw_recovery_ok' => 'A mail has been dispatched to your address with further details',
    'submit_item' => 'Submit',
    'notifications' => 'Notifications',
    'profile' => 'Profile',
    'admin_area' => 'Admin area',
    'reset' => 'Reset',
    'logout' => 'Logout',
    'imprint' => 'Imprint',
    'terms_of_service' => 'ToS',
    'last_commit' => 'Last commit: :diff',
    'open_issues' => 'Open issues: :count',
    'forks' => 'Forks: :count',
    'repo_seems_active' => 'Project seems active',
    'repo_seems_inactive' => 'Project seems inactive',
    'random_items_hint' => 'You may also like...',
    'item_creator' => 'By :creator',
    'item_submitted_by' => 'Submitted by <a href=":url">&#64;:user</a>',
    'reviews' => 'Reviews',
    'review_count' => ':count reviews',
    'no_more_items' => 'Now more items',
    'edit_item' => 'Edit item',
    'delete_item' => 'Delete item',
    'lock_item' => 'Lock item',
    'edit_profile' => 'Edit profile',
    'items_by_user' => 'Submitted items by this user',
    'reviews_by_user' => 'Reviews by this user',
    'avatar' => 'Avatar',
    'select_avatar' => 'Select avatar',
    'location' => 'Location',
    'bio' => 'Bio',
    'twitter' => 'Twitter',
    'password_confirmation' => 'Confirm password',
    'subscribe_newsletter' => 'I want to receive newsletters',
    'save' => 'Save',
    'write_review' => 'Write review',
    'review_content_placeholder' => 'Write something about this product',
    'submit_review' => 'Submit review',
    'review_stored' => 'Review has been stored',
    'already_reviewed' => 'You have already reviewed this product. In order to create a new review, you have to delete your old one.',
    'removal_successful' => 'Removal successful',
    'profile_saved' => 'Profile has been saved',
    'edit_item' => 'Edit item',
    'select_logo' => 'Select logo',
    'select_file' => 'Select file (ZIP)',
    'item_logo' => 'Logo',
    'submit_item' => 'Submit item',
    'submit' => 'Submit',
    'invalid_github_link' => 'Please specify a valid GitHub link',
    'upload_size_exceeded' => 'The specified uploaded file is too big in size',
    'item_type' => 'Type',
    'item_select_type' => 'Select type',
    'item_input_creator' => 'Creator',
    'item_summary' => 'Summary',
    'item_description' => 'Description',
    'item_tags' => 'Tags',
    'item_download' => 'Download',
    'item_github' => 'GitHub',
    'item_twitter' => 'Twitter',
    'item_website' => 'Website',
    'item_name_placeholder' => 'Enter a name',
    'item_creator_placeholder' => 'Enter creator',
    'item_summary_placeholder' => 'Enter a short summary',
    'item_description_placeholder' => 'Enter a description',
    'item_tags_placeholder' => 'E.g. tag1 tag2 tag3 tag4',
    'item_download_placeholder' => 'Enter download link',
    'item_github_placeholder' => 'Enter link to projects GitHub repository',
    'item_twitter_placeholder' => 'Enter Twitter handle',
    'item_website_placeholder' => 'Enter the projects website',
    'item_logo_hint' => 'Please use landscape layout',
    'item_tags_hint' => 'Tags separated by spaces, e.g. tag1 tag2 tag3 etc',
    'item_saved_successfully' => 'Item data has been updated',
    'item_not_found' => 'Item not found',
    'register_welcome_short' => 'Welcome aboard!',
    'register_welcome_long' => 'Welcome to ' . env('APP_NAME') . '! Be sure to complete your <a href=":url">profile</a> first. Have a nice time!',
    'item_approved_long' => 'Welcome to ' . env('APP_NAME') . '! You can go to your <a href=":url">profile</a> in order to edit your data.',
    'item_approved_short' => 'Your item has been approved',
    'item_approved_long' => ':name has just been approved. You can edit your item via its <a href=":url">page</a>. You can also write a first review!',
    'item_submitted_successfully' => 'Your item is now in review progress. You will be notified when the item is approved.',
    'review_added_short' => 'Your item submission has been reviewed',
    'review_added_long' => ':reviewer has reviewed your item <a href=":url">:item_name</a>',
    'mail_subject_item_approval' => 'Resource item approval',
    'mail_item_approved_title' => 'Resource item approved',
    'mail_item_approved_body' => 'Your submitted item :name has been approved! Click the link below to view the item. Note: You can also edit your item details.',
    'mail_view_item' => 'View item',
    'success' => 'Success',
    'error' => 'Error',
    'notice' => 'Notice',
    'about' => 'About',
    'logo' => 'Logo',
    'logo_info' => 'Set app logo (PNG only)',
    'cookie_consent' => 'Cookie consent',
    'cookieconsent_description' => 'Set your cookie consent content here',
    'reg_info' => 'Reg Info',
    'reginfo_description' => 'This info will be shown on the registration form',
    'tos' => 'Terms of Service',
    'tos_description' => 'Enter the terms of service content here',
    'imprint_description' => 'Enter the imprint content here',
    'head_code' => 'Head code',
    'headcode_description' => 'This code will be inserted in the head-tag of each page',
    'users' => 'Users',
    'get_user_details' => 'Get user details',
    'username' => 'Username',
    'reset_password' => 'Reset password',
    'profile_avatar_hint' => 'Avatars will be resized to 128x128 px',
    'locked' => 'Locked',
    'admin' => 'Admin',
    'delete_account' => 'Delete account',
    'approvals' => 'Approvals',
    'table_search' => 'Search',
    'table_show_entries' => 'Show entries',
    'item_id' => 'ID',
    'item_name' => 'Name',
    'item_user' => 'User',
    'approve' => 'Approve',
    'decline' => 'Decline',
    'table_row_info' => '',
    'table_pagination_prev' => 'Previous',
    'table_pagination_next' => 'Next',
    'report_successful' => 'Reported successfully',
    'report_id' => 'ID',
    'report_entity' => 'Entity',
    'report_type' => 'Type',
    'report_count' => 'Count',
    'report_lock' => 'Lock',
    'report_delete' => 'Delete',
    'report_safe' => 'Set safe',
    'report' => 'Report',
    'reports' => 'Reports',
    'report_confirm_lock' => 'Do you really want to lock this entity?',
    'report_confirm_delete' => 'Do you really want to delete this entity?',
    'report_confirm_safe' => 'Do you want to set this entity safe?',
    'item_approved' => 'Item has been approved',
    'data_saved' => 'Data has been saved',
    'cookie_consent_close' => 'Okay.',
    'mail_footer' => 'Kind regards',
    'newsletter' => 'Newsletter',
    'subject' => 'Subject',
    'content' => 'Content',
    'newsletter_in_progress' => 'Newsletter sending is now in progress',
    'send' => 'Send',
    'delete_account' => 'Delete account',
    'deleted_account_successfully' => 'Your account has been deleted',
    'no_notifications_yet' => 'No notifications yet.'
];