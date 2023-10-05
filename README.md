# Optimized Checkout for Woocommerce
This plugin improves your WooCommerce checkout page, making it simpler and similar to Shopify's checkout. It breaks down the checkout into easy steps for a better shopping experience. If someone has shopped with you before, they only need to do the payment step, making it even quicker.

Certainly! If you find the code I provided helpful and would like to support further development or show your appreciation, consider making a donation. Your contribution would be greatly appreciated and would help me continue to provide valuable assistance. Thank you for your support!

<a href="https://www.paypal.com/paypalme/MyVitamin" target="_blank"><img src="https://user-images.githubusercontent.com/17750115/272989771-8ef5163f-c96b-4d65-ac44-cf9bbf81f9da.png"  width="150" /></a>


## Author
I'm Jimish Soni, a full-time freelancer with over 9 years of experience creating WordPress websites. When I'm not working, you'll often find me enjoying some PlayStation time or taking trips to the seaside with my lovely wife for our vacations.


## Getting Started
1. Donwload the code and upload 'woocommerce' folder to your WordPress theme
2. Add following line of code to your functions.php file
   ```
   require get_theme_file_path( '/woocommerce/optimized-checkout/oc-checkout.php' );
   ```
3. Do following setting in woocommerce settings, go to:
   ```
   WooCommerce > Settings > Shipping > Shipping Options : Shipping destination -> Default to customer shipping address
   ```

## File Struture
- Inside woocommerce folder, there is a folder with lable optimized-checkout which does most of the magic. It consist following files:
  
| File | Description |
| --- | --- |
| checkout-template.php | This is the template file which will be used instead of page.php so we do not have unwanted elemenets on page. |
| oc-checkout.php | This file has all the hooks and filters code. |
| oc-style.css | All css code for styling is in this file. There is list of variable at the top of the file, which you can use to easily change fonts and colors |
| oc-jquery.js | This file contains validation and other jquery code for switching between steps |

## Screenshots
![optimized-woocommerce-checkout-screenshot-1](https://github.com/jimishsoni1990/optimized-checkout/assets/17750115/7a020d7c-a609-4a54-badd-bcc33642cfb8)
- Step 1
  
![optimized-woocommerce-checkout-screenshot-2](https://github.com/jimishsoni1990/optimized-checkout/assets/17750115/84230dcf-34ef-4750-b3ae-95dce651d788)
- Step 2
  
![optimized-woocommerce-checkout-screenshot-3](https://github.com/jimishsoni1990/optimized-checkout/assets/17750115/587bad8a-1688-44f3-a8b3-4c4e5729837d)
- Step 3

![faster-checkout-process-for-logged-in-users](https://github.com/jimishsoni1990/optimized-checkout/assets/17750115/925c84c0-c467-4b78-a033-0f795fc40eea)
- If user is retruning customer, checkout steps are reduced to only payment with all other information filled automtically. 

