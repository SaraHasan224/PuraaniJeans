<?php

return [
    'general'                          => [
        'failed'               => 'Request Failed',
        'success'              => 'Request Successful',
        'validation'           => 'Validation Error',
        'crashed'              => 'Something went wrong',
        'unauthenticated'      => 'Authentication Failed',
        'unauthorized'         => 'Authorization Failed',
        'access_denied'        => 'You do not have access.',
        'access_denied_record' => 'You do not have access to view this record!',
        'banner_size'          => 'Banner size cannot be greater than 2mb',
    ],
    'feature_alerts'                   => [
        'store'                      => 'Feature alert created successfully.',
        'update'                     => 'Feature alert updated successfully.',
        'delete'                     => 'Feature alert deleted successfully.',
        'feature_alert_image'        => 'The image field is required.'
    ],
    'user'                             => [
        'created'                   => 'User created successfully',
        'profile_update'            => 'Profile updated successfully.',
        'successfully_update'       => 'User updated successfully.',
        'bulk_delete_success'       => 'Deleted successfully.',
        'bulk_delete_errors'        => 'Unable to delete selected users successfully.',
        'phone_taken'               => 'This phone number is already registered.',
        'expired_code'              => 'Invalid or expired code provided, please try again.',
        'invalid_access_forbidden'  => 'You don\'t have access to this portal. Please contact your network administrator',
        'invalid_password'          => 'You have entered an invalid email or password',
        'verification_code'         => 'Hi there! A 6 digit verification code has been sent to your registered email address, please verify it below.',
        'otp_sent'                  => 'OTP has been sent successfully, which will be valid for ' . config('app.OTP_EXPIRE_TIME') . ' minute.',
        'email_not_exists'          => 'User with this email address and password does not exists or is not active.',
        'email_exists'              => 'This email address is already registered.',
        'login_attempts_expired'    => 'Login Attempts Expired. Your account has been blocked, please contact admin for further details.',
        'phone_and_email_exists'    => 'User with this email address and phone number already exists.',
        'merchant_account_inactive' => 'Your account is not active, please contact your administrator.',
        'agency_account_inactive'   => 'Your account is not active, please contact your administrator.',
        'email_logout'              => 'You will be logged out of your account after the email address has been changed.',
        'password_logout'           => 'You will be logged out of your account after the password has been changed.',
        'phone_number_logout'       => 'You will be logged out of your account after the phone number has been changed.',
        'plus_sign_not_acceptable'  => 'Email address should not contain plus (+) sign.',
        'not_exist'                 => 'User does not exist.',
    ],
    'agency'                             => [
        'created'                   => 'Agency created successfully',
        'not_exist'                 => 'Agency does not exist.',
        'merchant_assigned'         => 'Merchant(s) assigned successfully.',
        'merchant_unassigned'       => 'Merchants unassigned successfully.',
        'merchant_login_assigned'   => 'Login access assigned to agency.',
        'merchant_login_unassigned' => 'Login access unassigned from agency.',
        'agency_active'             => 'Agency activated.',
        'agency_deactive'           => 'Agency deactivated.',
        'merchant_account_login'    => 'Merchant account logged in successfully.',
        'merchant_account_not_login'=> 'Merchant account cannot be logged in.',
        'agency_account_login'      => 'Agency account logged in successfully.',
        'bulk_delete_agencies'      => 'Deleted successfully.',
        'bulk_cannot_delete_agencies'=> 'Agencies which are assigned to merchants cannot be deleted.',
        'access_denied'             => 'Access Denied.',
        'agency_not_verified'       => 'Agency needs to setup their account first.',

    ],
    'master_admin' => [
        'merchant_account_login'      => 'Merchant account logged in successfully.',
        'merchant_account_not_login'  => 'Merchant account cannot be logged in.',
        'merchant_account_inactive'   => 'Merchant account is inactive!',
        'token_not_found'             => 'Merchant account login token not found.',
    ],
    'password'                        => [
        'invalid_token'               => 'Invalid token provided',
        'is_not_unique'               => 'Please enter different password, it should not match previous four passwords.',
        'success'                     => 'Password Updated Successfully',
        'email_sent'                  => 'We have e-mailed your password reset link!',
        'token_expired'               => 'Token expired, please request again',
        'remaining_attempts'          => 'You may have entered incorrect password, remaining login attempts:',
        'incorrect_previous_password' => 'Incorrect previous password',
        'incorrect_password'          => 'Incorrect password',
        'correct_password'            => 'Correct password',
    ],
    'business'                         => [
        'updated'             => 'Business settings updated successfully.',
        'submit_application'  => 'Business application submitted successfully.',
        'approve_application' => 'Business application status changed successfully.',
        'file_uploaded'       => 'File uploaded successfully.',
        'feature_update'      => 'Business feature settings updated successfully.'
    ],
    'product'                         => [
        'updated'                     => 'Product settings updated successfully.',
        'cnic_settings_update'        => 'CNIC settings updated successfully.',
        'cnic_amount_update'          => 'Minimum required amount updated successfully.',
        'minimum_order_amount_update' => 'Minimum required amount for order updated successfully.',
        'product_settings_updated'    => 'Merchant product settings updated successfully.',
        'disable_bshop'               => 'Your Instant shop has been disabled',
    ],
    'import'                          => [
        'file_uploaded'               => 'File uploaded successfully.',
        'file_imported'               => 'Products imported successfully.',
        'file_not_imported'           => 'File not imported.',
        'file_type'                   => 'File type must be CSV.',
        'file_empty'                  => 'There is no data in your CSV.',
        'duplicate_columns'           => 'Please select unique column for each.',
        'required_fields'             => 'Required fields must be filled.',
        'import_file_headers'         => 'File should include column headers.',
        'invalid_column'              => 'Columns should be selected as mentioned in sample file.',
        'import_products_limit'       => 'Max limit to upload records is ' .config('app.IMPORT_PRODUCTS_LIMIT'). ' rows at a time.',
    ],
    'buyer_protection'                => [
        'updated'                     => 'Buyer protection setting updated successfully.',
    ],
    'categories'                      => [
        'saved'                       => 'Category saved successfully.<br>Please publish all the changes to reflect on your store site.',
        'updated'                     => 'Selected categories updated successfully.<br>Please publish all the changes to reflect on your store site.',
        'deleted'                     => 'Selected categories deleted successfully.<br>Please publish all the changes to reflect on your store site.',
        'category_exists'             => 'Category with this name already exists.',
        'products_exists_in_category' => 'Category which contains products can not be deleted.',
        'sequence_updated'            => 'Categories sequence updated successfully.<br>Please publish all the changes to reflect on your store site.',
        'select_store'                => 'Please apply store filter only to set category sequence.',
    ],
    'app_banners'                     => [
        'saved'                         => 'Banner saved successfully',
        'deleted'                       => 'Banner deleted successfully',
        'limit'                         => 'You can add only 1 active secondary type banner.',
        'footer_limit'                  => 'You can add only 1 active cart banner type banner',
        'cart_banner'                   => 'At least one cart banner must be active!',
        'secondary_banner'              => 'At least one secondary banner must be active!',
        'sequence_updated'              => 'App banners sequence updated successfully.',
        'sort_fail'                     => 'Please clear out filters to set banner sequence.',
    ],
    'app_quick_actions'                     => [
        'saved'                         => 'Quick Action saved successfully',
        'deleted'                       => 'Quick Action deleted successfully',
        'limit'                         => 'You have reached the maximum limit of ' .config('app.APP_QUICK_ACTION_LIMIT'). '.',
        'sequence_updated'              => 'App Quick Actions sequence updated successfully.',
    ],
    'contract_detail'                  => [
        'updated' => 'Contract details updated successfully.'
    ],
    'direct_bank_transaction_settings' => [
        'updated' => 'Settings updated successfully.'
    ],
    'payment_method'                   => [
        'gateway_not_exists' => 'Payment gateways for this payment method does not exists.',
        'sequence_updated'   => 'Payment methods sequence updated successfully.',
        'access_denied'      => 'Access for this payment method has been denied by administrator, please contact them with your query.',
    ],
    'merchant_payment_method'          => [
        'updated' => 'Payment method settings updated successfully.',
        'last_pm' => 'At least one payment method must be active!',
    ],
    'billing_charges'                  => [
        'updated' => 'Gateway settings updated successfully.'
    ],
    'bank_details'                     => [
        'updated'   => 'Bank details updated successfully.',
        'not_found' => 'Please add your bank details to continue.'
    ],
    'merchant_order_settings'          => [
        'updated' => 'Settings updated successfully.'
    ],
    'merchant_wallet'                  => [
        'withdrawal_request_created' => 'Withdrawal request sent successfully!',
        'insufficient_balance'       => 'Amount should be less than or equal to available balance.',
        'invalid_deposit_amount'     => 'Deposit amount should be greater than or equal to :available_balance.',
        'zero_deposit'               => 'Deposit amount should not be equal to zero.',
        'withdrawal_status'          => 'Withdrawal status updated successfully!',
        'settings_updated'           => 'Wallet settings updated successfully!',
        'deposit_success'            => 'Amount deposited successfully!',
        'withdrawal_status_updated'  => 'Withdrawal request statuses updated successfully!',
        'settlement_success'         => 'Your uploaded transactions settled successfully!',
    ],
    'merchant'                         => [
        'updated'                 => 'Merchant status updated successfully!',
        'app_created'             => 'Application updated successfully!',
        'app_deleted'             => 'Application deleted successfully!',
        'credentials_created'     => 'Application credential generated successfully!',
        'payment_method_updated'  => 'Payment method updated successfully!',
        'max_envs'                => 'Maximum allowed number of environments are created.',
        'store_url'               => 'Please setup your store URL in store settings to continue.',
        'invalid_store_url'       => 'Invalid store URL provided, please update your store settings or select correct store.',
        'credentials_updated'     => 'Credentials updated successfully.',
        'credentials_not_exists'  => 'Credentials for selected gateway or environment does not exists, please provide credentials first.',
        'env_not_active'          => 'At least 1 environment (Production/Sandbox) should be active before activating any prepayment method.',
        'credentials_regenerated' => 'After your credentials are changed, please update them on your website also.',
        'bank_details_not_exists' => 'To enable Direct Bank Transfer bank details need to be provided first, no bank details found!',
        'status_updated'          => 'Environment status updated successfully!',
        'company_exist'           => 'Company with same name or domain already exists!',
    ],
    'roles'                            => [
        'created'         => 'Role created successfully.',
        'updated'         => 'Role updated successfully.',
        'deleted'         => 'Role deleted successfully.',
        'name_not_unique' => 'Role with same name already exist'
    ],
    'branding'                         => [
        'created'                => 'Image created successfully.',
        'uploaded'               => 'File uploaded successfully',
        'updated'                => 'Image updated successfully.',
        'deleted'                => 'Image deleted successfully.',
        'failed'                 => 'Unable to delete brand logo.',
        'gtm_settings_updated'   => 'Google Tag Manager settings updated successfully!',
        'theme_settings_updated' => 'Theme color settings updated successfully!',
        'checkout_btn'           => 'Updated successfully.',
        'payment_gateway_icon'   => 'Payment gateway icon updated successfully.',
        'invalid_store'          => 'Invalid store selected!',
        'about_us_page_details'  => 'Details updated successfully.',
        'private_policy_details' => 'Policy details updated successfully.',
        'about_us_page_toggle_disable'  => 'Store About Us details disabled.',
    ],
    'bsecure_category'           => [
        'created'                    => 'Category created successfully.',
        'updated'                    => 'Category updated successfully.',
        'deleted'                    => 'Category deleted successfully.',
        'category_not_found'         => 'Category does not exits!',
        'category_error'             => 'Category will not become a child category.',
        'already_exist'              => 'Category name has already been taken.',
        'empty_mapping_data'         => 'Nothing to map.',
        'save_mapping'               => 'Category mapping saved successfully.',
        'category_association_err'   => 'This category has associated products and cannot be deleted.',
        'unique_category'            => 'Select unique featured categories.',
        'featured_category'          => 'Featured categories updated successfully.',
        'product_count'              => ':category_name category doesn\'t contain any product.',
    ],
    'email_sms'                        => [
        'created' => 'Email/Sms settings updated successfully.',
        'updated' => 'Email/Sms settings updated successfully.'
    ],
    'environment'                      => [
        'created' => 'Environment created successfully.',
        'updated' => 'Environment updated successfully.',
    ],
    'document'                         => [
        'updated'       => 'Document status updated successfully!',
        'notAvailable' => 'This document is no longer available.',
        'deleted'       => 'Document deleted successfully!'
    ],
    'shipment'                         => [
        'updated'                   => 'Shipment method updated successfully!',
        'not_updated'               => 'At least one shipment method must be active!',
        'credentials_not_exists'    => 'Please add Bykea credentials first!',
        'shipment_not_found'        => 'Shipment method does not exist!',
        'sequence_updated'          => 'Shipment methods sequence updated successfully.',
        'no_default_shipment'       => 'At least one shipment method must be default!',
        'default_shipment_disable'  => 'Default shipment method cannot be disabled!',
        'default_shipment_enabled'  => 'Please enable your shipment method to mark it default!',
        'merchant_shipment_not_found' => 'Please enable your shipment method first!',
        'store_shipment_mapping'      => 'Store shipment mapping saved successfully!',
        'store_shipment_values'       => 'Please select unique value for each status.',
        'woocommerce_shipment_status' => 'Woocommerce statuses synced successfully.',

    ],
    'merchant_shipment_area'        => [
        'city_required'             => 'To enable area base shipment charges, please add atleast one city!',
        'area_exist'                => 'Area already exists!',
        'delete_area'               => 'Area deleted successfully!',
        'update_bulk_charges'       => 'Area charges update successfully!',
        'location_details'          => 'Address updated successfully!',
    ],
    'global_settings'                  => [
        'updated' => 'global settings updated successfully!',
        'updated_timestamps' => 'Time Stamps updated successfully!',
    ],
    'order'                            => [
        'not_found'                 => 'Order not found.',
        'created'                   => 'Order created successfully.',
        'updated'                   => 'Order status updated successfully.',
        'access_denied'             => 'You do not have access to view this record!',
        'abandoned_reminder_sent'   => 'An email and SMS will be sent to your customer shortly.',
        'reason_saved'              => 'Reason saved successfully.',
        'order_updated'             => 'Order updated successfully.',
        'order_payment_status'      => 'Order payment status updated successfully.',
        'order_payment_mode_success'  => 'Order payment mode updated successfully.',
        'order_payment_mode_failure'  => 'Cannot proceed this request.',
        'payfast_gateway'             => 'Please enable payfast gateway with valid credentials.',
        'invalid_order_payment_mode'  => 'Invalid order payment mode.',
        'order_international_shiping' => 'Please select valid address from dropdown!',
        'order_edit_discount_applied' => 'Only new item can be added if discount is applied.'
    ],
    'value_added_services'             => [
        'updated'       => 'Value added services updated successfully.',
        'rates_updated' => 'Value added services rates updated successfully.'
    ],
    'payment_method_rates'             => [
        'updated' => 'Payment method Rates updated successfully!',
    ],
    'taxes'                            => [
        'created' => 'Tax created successfully.',
        'updated' => 'Tax updated successfully.',
        'deleted' => 'Tax deleted successfully.'
    ],
    'products'                         => [
        'saved'              => 'Product saved successfully.<br>Please publish all the changes to reflect on your store site.',
        'no_product_found'   => 'No product found in selected store against provided SKU!',
        'out_of_stock'       => 'This product is currently out of stock and unavailable.',
        'deleted'            => 'Selected products deleted successfully.<br>Please publish all the changes to reflect on your store site.',
        'updated'            => 'Selected products updated successfully.<br>Please publish all the changes to reflect on your store site.',
        'product_sku_exists' => 'Product with same SKU already exists.',
        'published'          => 'Product catalog published successfully.',
        'max_product_price'  => 'Maximum price for purchase item/product should be less than or equal to: ',
        'sync_in_progress'   => 'PIM products syncing has been started',
        'not_found'          => 'Product not found',
        'not_export'         => 'Products export failed',
        'rankings_updated'         => "Product rank's updated successfully",
        'rankings_import_limit'    => 'Maximum '.config('app.IMPORT_RANKING_LIMIT').' records allowed in CSV file.',
        'empty_csv'                => "Records doesn't exists in csv",
        'rankings_not_import'      => 'Products ranking import failed',
        'rankings_not_export'      => 'Products ranking export failed',
        'featured_product_updated' => 'Selected product updated successfully',
        'featured_product_deleted' => 'Selected product deleted successfully',
        'featured_product_store'   => 'Please select the Merchant Store',
        'featured_product_product' => 'Please select the product',
        'featured_product_featured_by' => 'Featured by updated',
        'featured_product_sequence_updated' => 'Featured products sequence updated successfully.',
    ],
    'stores'                           => [
        'created'                   => 'Store created successfully.',
        'updated'                   => 'Store updated successfully.',
        'deleted'                   => 'Selected stores deleted successfully.',
        'create_store_first'        => 'Please create your store first.',
        'url_exists'                => 'Store with this URL already exists.',
        'name_exists'               => 'Store with this name already exists.',
        'integration_type_exists'   => 'Store with this URL and integration type already exists.',
        'settings_updated'          => 'Journey settings updated successfully.<br>Please publish all the changes to reflect on your store site.',
        'no_default_store'          => 'There must be atleast one default store',
        'default_store_area'        => 'To enable area based shipment, please add atleast one city',
        'sequence_updated'          => 'Featured stored sequence updated successfully.',
        'featured_store_create'     => 'Featured store created successfully.',
        'featured_store_deleted'    => 'Selected store deleted successfully.',
        'featured_stores_limit'     => 'You cannot make more than 20 stores as featured.',
        'featured_stores_sort_fail' => 'Please clear out filters to set store sequence.',
        'one_page_checkout_message' => 'This is a beta checkout and may cause issues in your order processing. Please contact <a href="mailto:builder@bsecure.pk">builder@bsecure.pk</a> before enabling.',
        'magento_validation'        => 'Username & password fields are required.',
        'magento_username'          => 'Username is required.',
        'magento_password'          => 'Password is required.',
    ],
    'pick_of_the_day'                  => [
        'product_added'             => 'Selected product added successfully',
        'product_deleted'           => 'Selected product deleted successfully',
        'product_status'            => 'Selected product status updated successfully',
        'product_sequence_updated'  => 'Products sequence updated successfully.',
        'not_export'                => 'Products export failed',
    ],
    'product_group'                    => [
        'added'                   => 'Product group added successfully',
        'deleted'                 => 'Selected group deleted successfully',
        'not_exists'              => 'Product group doest not exists',
        'mapping_deleted'         => 'Selected product deleted successfully',
        'mapping_saved'           => 'Product added successfully',
        'duplicate_mapping'       => 'Duplicate product cannot be added in same group',
    ],
    'category_group'                   => [
        'added'                   => 'Category group added successfully',
        'deleted'                 => 'Selected group deleted successfully',
        'not_exists'              => 'Category group doest not exists',
        'mapping_deleted'         => 'Selected category deleted successfully',
        'mapping_saved'           => 'Category added successfully',
        'duplicate_mapping'       => 'Duplicate category cannot be added in same group',
    ],
    'variant'                          => [
        'updated'   => 'Varaint updated successfully.',
    ],
    'wallet_messages'                  => [
        'topup_reminder'       => '% of your bSecure wallet limit is exhausted. Kindly top up your bSecure wallet to avoid any inconvenience.',
        'wallet_limit_reached' => 'Your bSecure wallet limit has reached. Please top up your bSecure wallet to avoid any inconvenience.',
    ],
    'pre_payment_fees'                 => [
        'updated' => 'Prepayment Fees updated successfully.',
    ],
    'environment_variable'             => [
        'added'   => 'Environment variable added successfully.',
        'updated' => 'Environment variable updated successfully.',
        'deleted' => 'Environment variable deleted successfully.',
    ],
    'discounts'                        => [
        'created'        => 'Discount created successfully.',
        'updated'        => 'Discount updated successfully.',
        'deleted'        => 'Selected discount deleted successfully.',
        'name_exists'    => 'Discount with this name already exists.',
        'voucher_exists' => 'Voucher code already exists, please create new.',
        'access_denied'  => 'You do not have access.',
        'permissions'    => [
            'updated' => 'Discounts Permission Updated Successfully'
        ],
        'vouchers'       => [
            'created' => 'Discounts Voucher Created Successfully',
            'updated' => 'Discounts Voucher Updated Successfully',
        ]
    ],
    'plugins'                          => [
        'version_exists'            => 'Plugin with same version already exists',
        'saved'                     => 'Plugins version saved successfully',
        'created'                   => 'Plugins version created successfully',
        'updated'                   => 'Plugins version updated successfully',
        'deleted'                   => 'Selected versions deleted successfully',
        'inactive_reason'           => 'No order placed in last thirty days.',
        'inactive_description'      => 'Change to Inactive due to no order placed.',
        'plugin_activated'          => 'Plugin activated.',
        'shopify_plugin_disabled'   => 'Builder uninstalled private app from Shopify'
    ],
    'bin_codes'                        => [
        'updated'    => 'Bin codes updated successfully.',
        'deleted'    => 'Bin codes deleted successfully.',
        'not_unique' => 'Bin codes must be unique.',
        'list'       => [
            'deleted' => 'Bin codes list deleted successfully.',
        ]
    ],
    'tags'                             => [
        'saved'      => 'Tag saved successfully!',
        'tag_exists' => 'Tag with this name already exists!',
        'deleted'    => 'Selected tags deleted successfully!'
    ],
    'ratio'                            => [
        'image' => 'Image aspect ratio should be: ',
    ],
    'warehouses'                       => [
        'created'     => 'Warehouse created successfully!',
        'updated'     => 'Warehouse updated successfully!',
        'deleted'     => 'Warehouse deleted successfully!',
        'name_exists' => 'Warehouse with this name already exists!',
    ],
    'third_party_services'             => [
        'credentials_not_exists'  => 'Credentials for Orio does not exist. Please provide credentials first.',
    ],
    'alpha_tester'                     => [
        'created'  => 'Customer app alpha tester created successfully.',
        'updated'  => 'Customer app alpha tester updated successfully.',
        'deleted'  => 'Customer app alpha tester deleted successfully.',
    ],
    'customer'                         => [
        'updated'       => 'Customer type updated successfully.',
        'created'       => 'Customer created successfully.',
        'exist'         => 'This customer already exists in bSecure but with a different builder, you can search them by their phone number below after closing this pop up.',
        'phone_exist'   => 'Customer with this phone number already exists in bSecure but with a different builder, you can search them by their phone number below after closing this pop up.',
        'email_exist'   => 'Customer with this email already exists in bSecure but with a different builder, you can search them by their phone number below after closing this pop up.',
        'status_updated'    => 'Customer status updated successfully.',
    ],
    'invoices'                         => [
      'item_added'      => 'Invoice item added successfully',
      'item_removed'    => 'Invoice item removed successfully',
      'invoice_comment' => 'Invoice comment updated successfully',
      'created'         => 'Invoice created successfully',
      'updated'         => 'Invoice updated successfully!',
      'deleted'         => 'Invoice deleted successfully!',
      'saved'           => 'Invoice saved for later send!',
      'send'            => 'Invoice email send successfully!',
      'generated'       => 'Invoice generated successfully!',
      'invoice_image'   => 'The image field is required!'
    ],
    'store_areas'                      => [
        'added'          => 'Store area added successfully',
        'removed'        => 'Store area removed successfully',
        'city_exists'    => 'City already exists!',
        'country_exists' => 'Country already exists!',
        'enable'         => 'Area based shipping enabled',
        'disable'        => 'Area based shipping disabled',
    ],
    'additional_charges'               => [
    'created'       => 'Additional charges created successfully.',
    'updated'       => 'Additional charges updated successfully.',
  ],
    'order_notifications'              => [
    'muted'         => 'Instant order notifications muted successfully',
    'unmuted'       => 'Instant order notifications un-muted successfully.',
    'marked_seen'   => 'Instant order notifications marked seen successfully.',
    'subscribed'    => 'This user has subscribed to order notifications. Please ask user to re-login.',
    'un-subscribed' => 'This user has un-subscribed to order notifications. Please ask user to re-login.',
    'received'      => 'Order notification received successfully',
  ],
    'request_response_log'             => [
      'not_found'  => 'Request response logs does not exist!'
  ] ,
    'ip'                               => [
      'unblock_ip'  => 'Ip unblocked successfully.'
  ] ,
    'third_party_log'                  => [
      'not_found'  => 'Third party logs does not exist!'
  ],
    'campaigns'                        => [
    'created'                        => 'Campaign created successfully.',
    'updated'                        => 'Campaign updated successfully.',
    'image_selected'                 => 'Campaign image selected successfully.',
    'deleted'                        => 'Campaigns deleted successfully.',
    'product_added'                  => 'Campaigns product added successfully, please save to reflect changes.',
    'no_product_selected'            => 'No products found in file.',
    'invalid_column'                 => 'No. of columns and column names should be according to sample file.',
    'limit'                          => 'Maximum ' .config('app.MAXIMUM_CAMPAIGN_PRODUCTS_COUNT'). ' products can be added at a time.',
    'sort_fail'                      => 'Please clear out filters to set campaign product sequence.',
    'sequence_updated'               => 'Campaign products sequence updated successfully.',
    'campaign_not_found'             => 'Campaign does not exist!',
    'campaign_sale_created'          => 'New campaign sale created successfully.',
    'campaign_sale_updated'          => 'Campaign sale updated successfully.',
    'featured_campaign'              => 'Featured campaign updated successfully.',
    'campaign_sale_discount_updated' => 'Campaign sale discount updated successfully.',
    'campaign_sale_discount_deleted' => 'Campaign sale discount deleted successfully.',
    'campaign_discount_value'        => 'Discount value is required.',
    'campaign_discount_type'         => 'Discount type is required.',
    'campaign_discount_value_less'   => 'Discount value should be less then product total price.',
    'campaign_sale_time_validation'  => 'Sale end date time cannot be less than start date time!.',
    'featured_campaign_deactivate'   => 'Featured campaign cannot be deactivated.',
    'featured_campaign_delete'       => 'Featured campaign cannot be deleted.',
  ],
    'admin_notification_permission'    => [
    'deleted'                        => 'Admin notification deleted successfully.',
    'updated'                        => 'Admin notification allowed successfully.',
    'admin_notification_type_does_not_exist' => 'Admin notification type does not exist.',
    'user_type_admin_does_not_exist' => 'User type admin does not exist.',
    'invalid_admin_notification_type' => 'Invalid admin notification type.'
  ],
    'order_placeback'                  => [
        'saved'                        => 'Order placeback status saved successfully!',
    ],
    'no_record'                        => 'No record found.',
    'zoodpay'                          => [
      'success'        => 'Your request submitted successfully.',
      'failed'         => 'Your request cannot be processed, please check if your PIM is enabled and successfully configured. For further details contact at: <a href="mailto:help@bsecure.pk">help@bsecure.pk</a>',
      'request_exist'  => 'Request already submitted. You cannot submit this request again.',

    ],
];