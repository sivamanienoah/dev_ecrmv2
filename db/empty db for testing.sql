-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 07, 2013 at 11:39 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_ecrmv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `crms_additional_cats`
--

CREATE TABLE IF NOT EXISTS `crms_additional_cats` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `crms_additional_cats`
--

INSERT INTO `crms_additional_cats` (`cat_id`, `cat_name`) VALUES
(1, 'General'),
(2, 'Hosting'),
(3, 'Products'),
(4, 'Extras'),
(6, '3rd Party'),
(7, 'Realestate'),
(8, 'Discounts'),
(9, 'Domain Names'),
(10, 'Graphic Design'),
(11, 'Payment Terms'),
(12, 'Optimisation'),
(13, 'Packages'),
(14, 'Process'),
(15, 'V-Series'),
(16, 'SEO'),
(17, 'Synagize'),
(18, 'Subscriptions'),
(19, 'Terms');

-- --------------------------------------------------------

--
-- Table structure for table `crms_additional_items`
--

CREATE TABLE IF NOT EXISTS `crms_additional_items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `item_desc` text NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=147 ;

--
-- Dumping data for table `crms_additional_items`
--

INSERT INTO `crms_additional_items` (`itemid`, `item_desc`, `item_price`, `item_type`) VALUES
(15, 'HUBONLINE XML INTEGRATION\nConnection of selected HubOnline Data via XML feed directly into your website package for seamless integration and data consolidation. This charge does not include fees that HubOnline may charge you, the client for access to the XML data of your listings. Please discuss this with your account manager at REA/HubOnline.', '750.00', 6),
(13, 'USER MANAGEMENT SYSTEM (UMS)\nThis powerful system allows your to capture member registration data including name, surname, email, mobile, address, etc and build a database of registered users to your website. This is essential when unique and individual user accounts to login to special sections of a website is required or if you wish to integrate with your eCommerce website with different payment categories, ie. Wholesale, Retail, etc where the shoppers will need to login using their own user name and password to shop online.					', '2490.00', 3),
(2, 'LIGHTBOX IMAGE GALLERY\nImage thumbnails will be enlarged on an overlay that would mask the existing page and the enlarged image would be animated into place and displayed with controls to navigate through the image gallery.', '175.00', 4),
(3, 'EMAIL MARKETING\nNewsletterPRO Premium Edition\nA professional and value packed email marketing solution designed to help you manage customers and communicate using content rich html based email (SMS option available with Premium Edition SMS charges apply) NewsletterPRO Premium Edition provides Opt-in / Opt-out SPAM Act compliance and allows you to send up to 12,000 emails per day!\n\n*$350 once off setup and installation, including template customisation\n**$49.90 per month direct debit from valid credit card, minimum commitment of 12 months.', '350.00', 3),
(4, 'E-COMMERCE\nV-Shop Online Shopping System\nIf you are selling a product or service online and are seeking an easy to use eCommerce solution, then V-Shop is the choice for your business. V-Shop easily connects to an existing static website and takes you to the eCommerce arena accepting the entire transaction from your website.\nFeatures Included:\n- Content Management System\n- Product Catalogue\n- Shopping Cart facility + Checkout\n- Connection to PayPal directly from Checkout\n- Setup, installation and customisation\n- Business hours technical support\n- 1 hour of in-studio training', '1990.00', 3),
(45, 'EMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', 1),
(5, 'GOOGLE ANALYTICS\nConnection and installation of Google Analytics to live website providing the tools for the client to access and monitor traffic and overall website activity.					', '175.00', 4),
(6, 'FACEBOOK SHARING\nShare-This-Product feature allows visitors to your website to select a product of interest and have that product sent to their friends list in Facebook for their opinion, etc. This feature allows your product to be exposed to a public networking website of future and prospective website visitors.					', '175.00', 4),
(7, 'CONTENT MANAGEMENT SYSTEM\nWebPublisherCMS is an easy to use content management system residing on the back-end of your website. With site administrators access to designated pages, you can manage your website content yourself including text and images. WebPublisherCMS (1 x lifetime user license per domain).					', '990.00', 3),
(10, '***** FREE WEB HOSTING for the first 12 months *****', '-300.00', 8),
(19, 'GOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', 1),
(9, 'WIREFRAME + INTERFACE DESIGN\nWe will design a GUI (Graphical User Interface) and establish the new web page layout rules for master content pages and subsequent information pages. A new primary navigation panel and information architecture will be considered during this process. eNoah iSolution to establish a series of layout page designs which will form the basis of the remainder of the website and house the entire website content thereafter.', '2800.00', 10),
(11, 'JAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', 1),
(12, 'AJAX PROPERTY DETAIL OVERLAY\neNoah iSolution will add an enhanced display of property details masking over the current page. This feature will be installed on all property listing pages of the website. For a live example visit http://www.cityliving.com.au/buying-residential.php				', '1050.00', 7),
(14, 'MYDESKTOP (HTML) EMAIL TEMPLATES\nRich HTML email templates that can be installed into MyDesktop CRM providing the agent with a format that allows their listings to be emailed to their database in their company branding in line with their website design.\n\n*** PLEASE NOTE ***\nTemplates developed by eNoah iSolution can only utilise existing features of the Desktop system and therefore limited to what Desktop offer us.', '350.00', 6),
(18, 'GOOGLE MAPS\nIntegrated Google MAPS for website accessible via CMS control. Website administrator can simply add contact details to CMS and website will call exact Google MAP to appear via iFrame.', '175.00', 1),
(20, 'FLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '700.00', 1),
(16, '***** CLIENT DISCOUNT *****\nIn an effort to nurture solid business relations with you, eNoah iSolution is proud to offer a generous 10% discount on the above mentioned services.										', '0.00', 8),
(21, 'HOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n1GB Storage, 100GB Downloads, 10GB Uploads, PHP 5 + Apache 2, MySQL database, Unlimited email and Load-balanced servers.\n\nMaintenance:\nHourly backups, Free instant restores, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, CSS/XHTML fixes for new browser releases.\n\nSupport:\nBusiness hours technical support, software tele-training and assistance, unlimited assistance with POP/IMAP/WEB email setup (phone service available only).\n\nPAID MONTHLY via Mastercard, Visa or EFT only:', '39.90', 7),
(23, 'SHARED SSL CERTIFICATE\nAvailable as an add-on to the Power Cluster hosting package, access to our shared SSL certificate provides the ability to store part or all of a web site on one of our secure webservers and present those pages to a user and collect responses in a secure manner. SSL, or secure sockets layer, is a mechanism for web browsers to connect to web servers and encrypt the data sent in either direction for security requirements.\n\nShared SSL Certficate is charged on an annual basis.', '100.00', 2),
(25, 'DOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', 9),
(27, '*** OPTIONAL EXTRAS ***\n', '0.00', 4),
(28, 'CMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', 1),
(29, 'MYDESKTOP XML INTEGRATION\nConnection of selected MyDesktop Data via XML feed directly into your website package for seamless integration and data consolidation. \n*** Please note that there are charges from MyDesktop ti release the XML data feeds ***', '550.00', 6),
(30, 'XHTML / CSS CODING \nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):', '0.00', 1),
(31, 'SEARCH BY MAP\nA JavaScript Map Search will be designed and developed to provide a state by state map search. Results will appear in a listing page. Includes design, development and integration into the existing home page html.', '350.00', 1),
(32, 'PROPERTY QUICK SEARCH\nA ''Quick  Search'' feature will be integrated into the home page for site visitors to easily access property listings that meet their search criteria and display results in a property listing page.', '350.00', 7),
(33, 'AUTO PROPERTY FINDER\nVisitors can opt-in from your website and determine their property search criteria for your MyDesktop account to automatically alert them via email on the latest property listings that match your search criteria. This feature also allow the user to save their searched properties and save different search configurations.\n', '525.00', 7),
(34, 'PRINTABLE BROCHURE\nVisitors can print from a ''printer friendly'' page from your property detail page which will compose the property data in an A4 friendly format.', '175.00', 1),
(35, 'EMAIL-A-FRIEND\nVisitors can email a page from your website to their friends and colleagues allowing for external traffic to head into your website increasing site traffic and website activity.', '175.00', 1),
(36, 'DOMAIN NAME DELEGATION\nFrom supplied domain name (client to supply registry key and/or domain name password) eNoah iSolution will delegate your domain name to your new host server - 1 x domain name delegation.', '87.50', 9),
(37, 'DOMAIN NAME REDIRECTION\nFrom supplied domain names, eNoah iSolution will redirect your domain names to the desired URL for websites that have multiple domain names bringing traffic to them - 1 x domain name redirection.', '87.50', 9),
(38, 'FLASH INTERACTIVE NAVIGATION\nDesign and development of a Flash animated/interactive primary navigation panel to reside on all pages throughout the website and allow visitors to navigate their way through to the major sections of the website.', '700.00', 1),
(39, 'JAVASCRIPT INTERACTIVE NAVIGATION\nDesign and development of a JavaScript interactive primary navigation panel to reside on all pages throughout the website and allow visitors to navigate their way through to the major sections of the website.', '350.00', 1),
(40, 'SINGLE PAGE HOSTING (used with parked domains)\neNoah iSolution will host a single page on a supplied domain name. \nGeneral purpose of a single page on a parked domain is to generate leads to a specified website.\n\nSingle page hosting is billed annually.', '100.00', 2),
(55, 'LOGO DESIGN\n\nFrom supplied creative brief, eNoah iSolution are to research, develop and design a corporate logo for the client with consideration to both online and offline branding, target audience, industry sector and various other influences.\n\nThis quotation includes 3 individual and unique designs for your consideration and prospective approval. Upon successful approval and payment of your new logo concept the master vector files will be released.\n\n*IMPORTANT\nPrice includes 2 rounds of changes per design concept. Any additional changes will be charged out at our hourly studio rate.', '2100.00', 10),
(41, '*** WHOLESALE CLIENT DISCOUNT ***\nIn an effort to develop and maintain a mutually beneficial business relationship, eNoah iSolution is proud to offer you a generous 15% wholesale/resellers discount on the above mentioned web technology.', '0.00', 8),
(42, 'EDM/EMAIL MARKETING HTML TEMPLATES\neNoah iSolution are to design and develop a rich html email friendly template for viewing via email clients with a viewing dimension of no more than 650px wide and an unlimited height.', '350.00', 1),
(43, 'BUSINESS STATIONERY\nFrom approved logo, eNoah iSolution will design and supply press ready artwork of the following business stationery elements:\n\n- business card\n- letterhead\n- letterhead (MS Word template)\n- follower\n- fax header\n- with compliments slip\n- envelope', '1050.00', 10),
(44, 'INTEGRATED VIDEO GALLERY\neNoah iSolution will integrate Prop-Vid videos accessible via our WebPublisherCMS. By saving the Prop-Vid URL to the CMS the video will play through the live website providing for a seamless video experience.', '1050.00', 1),
(49, 'Thank you for entrusting eNoah iSolution with your web technology requirements. \nPlease see below an itemised breakdown of our service offering to you:', '0.00', 1),
(69, '\nADVANCED PROPERTY SEARCH\nAllowing your site visitors to enter the specific search criteria for the properties that are of interest to them. This feature is an upgrade to the standard ''Quick Search'' feature available with our Unlimited Website Package.', '875.00', 7),
(47, '\nDEDICATED SSL CERTIFICATE\nAvailable as an add-on to the Power Cluster hosting package, access to a dedicated SSL certificate provides the ability to store part or all of a web site on one of our secure webservers and present those pages to a user and collect responses in a secure manner. SSL, or secure sockets layer, is a mechanism for web browsers to connect to web servers and encrypt the data sent in either direction for security requirements.\n\nDedicated SSL Certficate is charged on an annual basis.', '400.00', 2),
(48, 'TIMEFRAME\nWe estimate completion of the above project would take place within a time frame of 6 weeks providing that all content and client cooperation is in place.', '0.00', 1),
(51, 'VIDEO PAGE\nAdd video files to the pages via the CMS through the WYSIWYG editor. A designated page will display the videos you add to the site or embed videos from video sharing sites such as YouTube.', '350.00', 1),
(52, 'ADDITIONAL PAGE FOR LOW BUDGET SITES\nInclude additional pages to any budget website package. This item includes a javascript drop-down menu that will enable the linkage of these pages to the main navigation.', '350.00', 1),
(58, '\nSEARCH BY GOOGLE MAP\neNoah iSolution will plot the agency''s property listing on a Google map. Properties will be marked by a branded pin that can be clicked to bring up property details.', '1050.00', 7),
(59, '\nCustomised Design GUI\n\nA tailored website design based around your liking. Totally ourside the parameters of the ''unlimited'' package.', '3000.00', 7),
(60, '\nWEBSITE KEYWORD SEARCH\nA search field will be added to the top of the website allowing users to quickly search the entire site for the search term. Results will be displayed in an easy to read and categorized format.', '700.00', 4),
(56, 'BLOG\nThe simple blogging tool provided by eNoah iSolution allows you to add posts and accept comments on the posts you published. You can share the posts with popular social networking sites and you can add tags and edit comments on the articles you published.', '700.00', 4),
(57, '\nWEBPUBLISHER SEO PLUGIN\n\nThe WebPublisher SEO plugin allows for title, header and keyword data to be defined on a page by page basis. Allowing for greater search engine optimization and key word targeting functionality than is not possible with a standard CMS.', '350.00', 4),
(61, '\nLANGUAGE TRANSLATOR (up to 3 languages)\nUsing the latest Google translation API eNoah iSolution will add AJAX driven site translation to the site. This will allow for the seamless translation of your sites content from English to any other three languages of your choice that are supported by the Google translator.\n\n***PLEASE NOTE***\nPlease note that this is a fully automated translation and as such doesn''t provide a complete contextual translation. ', '350.00', 4),
(63, 'SITE INFORMATION ARCHITECTURE\nWebsite architecture is our approach to the design and planning of your website which, like architecture itself, involves technical, aesthetic and functional criteria. As in traditional architecture, the focus is properly on the user and on user requirements. This requires particular attention to web content, a business plan, usability, interaction design and search engine optimisation (SEO). For effective SEO it is necessary to have an appreciation of how your website relates to the World Wide Web.', '0.00', 1),
(64, 'CONTENT MIGRATION\nWe will consolidate all the existing content under different domain names according to the site information architecture. The existing site content will be re-organised and transferred across to the new Content Management System that will be implemented. This will include text, images and internal links currently scattered across different areas. The migration of the content will require client corporation to identify dated or irrelevant content.', '15.00', 1),
(66, '\nOPTIONAL:\n\nBack-end skeleton site featuring a Content Management System, this will be purely for SEO purposes, and will not be viewable by the public, only by Search Bots.', '1340.00', 1),
(71, 'LOYALTY MANAGER\neNoah iSolution, propose to deploy a web-based (PHP/MySQL) incentive system designed to promote and reward the loyalty of new and existing retail customers. The system is designed to capture retail customer data during point of sale, provide the email marketing tools required to create and send electronic direct mail campaigns to selected groups, refer to data reports about the business and its daily activity and manage your customer and administrator contacts.', '4990.00', 3),
(70, '\nPROPERTY SEARCH BY GOOGLE MAP\nProviding a dynamic and interactive user experience to site visitors seeking property in the geographic location of their choice directly from your website. The functionality allows us to customise the google map with your map pin and logo to appear in the information balloon when hovering over a property listing.', '1050.00', 7),
(72, 'PROPERTY LISTING TRAFFIC REPORTING\nProperty specific reports emailed to you weekly informing you of the online performance of each individual property listing on your website. The report is emailed to you via a PDF complete with your logo and business contact details. This is great when showing vendors how much online interest their property listing is receiving via your website or for showing new prospects how current stock is performing online.', '1050.00', 7),
(73, 'MOBILE AGENCY WEBSITE\nThe mobile agency website provides the ever-evolving real estate agency the power to have their property listings with them on the go, ensuring no opportunity is lost when approached with another potential listing. The iPhone optimised Mobile Agency Website is your office website in a beautiful iPhone (touch experience) format drawing your listings from your XML data feed. This means the changes you make on your desktop website will be appear on your Mobile Agency Website, ensuring you have every angle covered when pushing your office and its listings to the market.', '1990.00', 7),
(74, 'PAYMENT TERMS\nIn order for us to proceed with your order, we require a minimum upfront payment amount of 50% to be paid into our bank account. We accept Mastercard, Visa and EFT. Unfortunately we do not accept American Express. Payments made via cheque will be accepted but will not be recognised until funds arrive.\n\nOur payment details are below:\n\nWestpac Banking Corporation\neNoah iSolution\nBSB: 032159\nACC: 175239', '0.00', 11),
(75, 'INTEGRATED CURRENCY CONVERTER\nThe ability to convert the currency on a property listing on your live website to any currency in the world without the need for the page to refresh. This is perfect for international prospects requiring a quick currency conversion on the spot to avoid interrupting the user experience. Comes with 3 different currencies to convert to; Additional currencies can be added at $87.50+GST each.', '350.00', 7),
(76, '[Unlimited Package] HOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n- Unlimited Email Accounts\n- 10GB Monthly Traffic\n- PHP 5 + Apache 2, \n- MySQL database, \n- Load-balanced servers\n- Statistics Report\n- Webmail Control Panel\n\nMaintenance:\n- Daily backups\n- Spam filtering\n- Email virus blocking\n- Performance Compression\n- Reliable Session Management\n- CSS/XHTML minor fixes for agreed browsers.\n\nSupport:\n- Business hours technical support\n- software tele-training and assistance\n- unlimited phone assistance with email configuration\n\nPAID MONTHLY by direct debit to credit card:', '49.90', 2),
(77, '\nV3 SECURE HOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n1GB Storage, 100GB Downloads, 10GB Uploads, PHP 5 + Apache 2, MySQL database, Unlimited email and Load-balanced servers.\n\nMaintenance:\nHourly backups, Free instant restores, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, CSS/XHTML fixes for new browser releases.\n\nSupport:\nBusiness hours technical support, software tele-training and assistance, unlimited assistance with POP/IMAP/WEB email setup (phone service available only).\n\nPAID MONTHLY via Mastercard, Visa or EFT only:', '89.90', 2),
(78, '- Special Properties\nDevelop into the CMS a feature that allows the agency to create a new set of pages (with a new server directory, ie. youragency.com.au/propertyname) to act as a standalone and dedicated microsite for the property listing that is deemed ''special'' and qualifies for its own website. Property data would be served from the core website and used to populate the DIY microsite feature which can handle unlimited microsites. Microsites include up to 4 pages and 1 registration form for call to action.', '5000.00', 7),
(79, '1 PAGE MINI-SITE\nThis product is designed to provide a quality online presence to a property for sale or lease and educate the site visitor with enough information on the property to move forward with an enquiry. Includes image gallery, floorplan link, video link, google map, contact information and an enquiry pointing to an email address of your choice. It also includes links back to your social media accounts and has a provision for your photo and phone contact details should you wish to be contacted directly from the site.', '700.00', 7),
(80, 'iPad Digital Listing Kit\nText to come', '69.90', 7),
(81, 'IE6 OPTIMISATION\nOptimisation of your website in this browser ensures your visitors will be able to view your website in a perfectly functional browsing experience and without any broken design elements the browser typically loads when XHTML and CSS is not optimised. This service includes optimisation for a working website in Internet Explorer 6 and does not promise to provide an exact representation of the website as it appears in other more stable browsers. Please call us on 1300 130 656 if this level of optimisation is insufficient for your needs.', '700.00', 12),
(82, '\nDESIGN\nFrom approved wireframe layouts, VT will design to 960px Web Ready specs, the following master pages for client review and approval:\n- Home\n- Master Content Layout 1\n- Master Content Layout 2\n- Call-to-action Registration\n\n*After first concepts are issued to client, there are 3 (inclusive) rounds of changes allowed thereafter. Additional creative works required will require an additional quotation prior to implementing the changes.\n\nTime : 12hrs', '2100.00', 10),
(83, 'OPEN HOUSE INSPECTION PLANNER\nThe Inspection Planner feature provides visitors to your website with the ability to select properties that are open for inspect in a given week and by simply clicking ''Get Directions'' a Google map is instantly generated plotting the best route of travel for your day to ensure you get to your Open Home Inspections on time. This section is applied to both Buying and Renting sections of your website.', '1000.00', 7),
(84, '\nINTERACTIVE MICRO-SITE PACKAGE\nPerfect for the small business start-up, our Interactive Micro-site package provides every detail you need to launch into your first website without compromising on quality. Our package provides you with a great design framework to work within; our powerful signature application WebPublisherCMS drives all 6 pages. \n\nThe following inclusions are available with this package:-\n\n- Custom design elements on fixed grid\n- 6 x pages all driven by our powerful WebPublisherCMS\n- Image gallery and Contact form (2 of the 6 pages)\n- Interactive JavaScript Slides', '1990.00', 13),
(85, '\nINTERACTIVE MICROSITE, HOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n100 email accounts, 5GB monthly traffic, 1 MySQL database, Statistics Report, Webmail Control Panel.\n\nMaintenance:\nDaily backups, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, CSS/XHTML minor fixes for agreed browsers.\n\nSupport:\nBusiness hours technical support, software tele-training and assistance, unlimited phone assistance with email configuration.\n\nPAID MONTHLY via Mastercard, Visa or EFT only:', '29.90', 2),
(86, 'PRE-PRODUCTION / PROJECT PLANNING\nProduce Wireframe + Functional Specifications documentation of all aspects of the project including:\n\n- Front-end Design & Development\n- Back-end programming and logic\n- Data Export expectations\n- Data Import/Capture expectations\n- Interactive Design and UX\n- Hosting, Maintenance + Support', '1400.00', 14),
(87, 'INTERFACE DESIGN\nConforming to existing branding and style guide, we will design a series of Graphical User Interface designs that adhere to the approved architecture and functionality proposed herein. We will provide a master page for all sections of the portal and require client approval before proceeding to XHTML/CSS coding.\n\n*Includes 5 x master page designs based on approved site-map and 3 rounds of design adjustments per page. \nAdditional changes/adjustments will be an additional charge.', '1400.00', 14),
(88, 'XHTML / CSS / JAVASCRIPT / AJAX DEVELOPMENT \nFrom the approved GUI design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS and the above mentioned scripting languages all required pages for this project. As mentioned there are 5 master pages to consider and base our core layout from; subsequent pages must also adhere to the master page layout rules.', '2800.00', 14),
(89, 'CONTENT MANAGEMENT SYSTEM\nCustomise and install WebPublisherCMS to accommodate the various areas of the new portal and provide content manageability to the portal administrators. Please visit this link to watch a video demonstration on WebPublisherCMS and how it works: http://clients.eNoah iSolution.com/webpublisher/webpublisher-demo.mov', '1400.00', 14),
(90, 'CUSTOM PROGRAMMING + DEVELOPMENT\nDevelop and programme all proposed functionality on the sitemap and ensure all areas are properly tested and debugged prior to go-live. This refers to all sections of the project, and considers all JavaScript. AJAX work specified in the functional spec along with all PHP works required to achieve objective.', '1400.00', 14),
(91, 'BROWSERS\nWe will develop this project to be compatible in the following browsers only:\n\n- IE8+\n- Safari (Mac)\n- Firefox (Mac + PC)\n- Chrome (Mac + PC)\n\n*All other browsers will incur additional charges if they are also required to render the website correctly.\n**This portal will not be optimised for iPad or iPhone or any other mobile device. Please advise if you require this project to be compatible on these devices and we will provide you with an additional quotation for this service.', '825.00', 14),
(92, 'CONTENT POPULATION\nWe will develop this website to contain all ''Shadow Content''. This is content we use to suggest our word and image counts for each page and in support of the design and SEO. We will provide ''Mock or Shadow'' text and images included in this service. Live content will be uploaded to the working website, replacing the Shadow content and we estimate the amount of live content to be handled, processed and uploaded will require approx. 8 hours of studio time.\n', '1400.00', 14),
(93, 'PAYMENT TERMS\nIn order for us to proceed with your order, we require a minimum upfront payment amount of 50% to be paid into our bank account. We accept Mastercard, Visa and EFT. Unfortunately we do not accept American Express. Payments made via cheque will be accepted but will not be recognised until funds arrive.\n\nOur payment details are below:\n\nWestpac Banking Corporation\neNoah iSolution\nBSB: 032159\nACC: 175239', '0.00', 14),
(94, 'HOSTING + DOMAIN\nHosting is not included in this quotation, however we require PHP / MySQL on Linux in order to develop the aforementioned technology for you. Domain name registration is not included in this quotation, please advise if you require us to register your domain name for you.\n\n*We will provide you with a quotation for the hosting separate and assume you have already made the purchase for the domain name.', '0.00', 14),
(95, 'DELIVERY\nWe will require 5 weeks of production time to design and develop this project. We will also require an additional 1 weeks for collaborative testing and content uploading prior to sending live. If all parties cooperate on milestone, this projects duration will be 6 weeks.', '0.00', 14),
(96, 'V-SERIES \n[ Base System ]\nThe V-Series Website Package is our pre-developed modular technology package that provides small to medium enterprise the ability to drive their online business demands with world-class technology at a fraction of the price of custom technology. V-Series is sold to you as a pre-developed packaged functionality and you simply purchase the modules you need!... Our base system comes with a powerful Content Management System to get you started and a custom user interface design to keep you on brand with your corporate image. V-Series base system includes 7 pages + CMS.', '2990.00', 15),
(97, '[ Unlimited Pages ]\nThis module allows you to have unlimited pages via our interactive drop-down menu with complete access to the Content Management System where you can edit, create and manage every page on your website. You require this module to support your unlimited galleries, video pages, promotional pages and more.', '2000.00', 15),
(98, '[ eCommerce Module ]\nConvert your website to a powerful Online Store, our eCommerce module brings the following functionality to your website: a 2 level product catalogue, shopping cart, delivery management, checkout and payment integration with PayPal, all manageable via the Content Management System.\n*Real-time payment gateways are subject to POA.', '3000.00', 15),
(99, '[ User Management Module ]\nManaging user data, customers, creating individual and unique user accounts for member login and quick search of users in your database can all be managed by our User Management Module. This is a must for any eCommerce website seeking control over members, users, administrators and inbound form data capture.', '2000.00', 15),
(100, '***PACKAGE TERMS - DESIGN***\n\nUser Interface Design or GUI is custom on a fixed-grid; this means we will provide an interface that is custom designed with respect to your colour, brand, font and general corporate styling however it must adhere to our strict layout fixed grid. In order to qualify for our packaged technology solution your website design must adhere to our grid and any deviations of the grid will incur additional charges for custom works. We will provide you with 1 x initial GUI design and you have 3 rounds of adjustments/amendments included in our base package price.', '0.00', 15),
(101, '***PACKAGE TERMS - LICENSING***\n\nDue to the nature of the V-Series Website Package being a pre-developed software technology, we require the technology to be served and hosted from our own secure Website Hosting environment. You are purchasing a license to use the V-Series Website Package technology and modules. Although we are installing and customising a package for your ongoing use the source code of the V-Series software remains the property of eNoah iSolution. Ongoing charges include: hosting, support & maintenance and are billed separately.', '0.00', 15),
(102, '***PACKAGE TERMS - HOSTING***\n\nDepending on the V-Series modules you purchase, depends on what your ongoing monthly charges will be. The base package ongoing Hosting, Technical Support & Maintenance charges start at $29.90+GST per month and will be invoiced on the 15th of each month; direct debit from approved MasterCard or Visa only. Additional hosting charges are applicable to each module you purchase. The more your website does, the more it costs to run and our ongoing charges are relative to the size of your website. A separate quotation will be raised for your ongoing charges.', '0.00', 15),
(103, '***PACKAGE TERMS - DELIVERY***\nWe will require 5 weeks of production time to design and develop this project. We will also require an additional 1 weeks for collaborative testing and content uploading prior to sending live. If all parties cooperate on milestone, this projects duration will be 6 weeks.', '0.00', 15),
(104, '***PACKAGE TERMS - PAYMENT***\nIn order for us to proceed with your order, we require the prescribed minimum upfront payment amount to be paid into our bank account. We accept Mastercard, Visa and EFT. Unfortunately we do not accept American Express. Payments made via cheque will be accepted but will not be recognised until funds arrive.\n\nOur banking details are below:\n\nWestpac Banking Corporation\neNoah iSolution\nBSB: 032159\nACC: 175239', '0.00', 15),
(105, '[ Custom Works Requested ]\n(a) Optional password protection on selected galleries (add $700+GST)\n(b) Pricing scale on multiple digital purchases in eStore (add $825+GST)', '0.00', 15),
(107, '***APPROVED PAYMENT PLAN***\nAs discussed, we estimate this project will take up to 8 weeks to deliver and have provided you with the following payment plan, should you wish to proceed we will require your 1st payment upfront and subsequent payments on their respective due dates:\n\n$998.75+GST = Payment 1 : Project Kick-Off\n\n$998.75+GST = Payment 2 : Week 2\n\n$998.75+GST = Payment 3 : Week 3\n\n$998.75+GST = Payment 4 : Week 4\n\n$998.75+GST = Payment 5 : Week 5\n\n$998.75+GST = Payment 6 : Week 6\n\n$998.75+GST = Payment 7 : Week 7\n\n$998.75+GST = Payment 8 : Project Go-live', '0.00', 15),
(106, '======================================================================\n\n[ Authorised Discount ]\n(a) Optional password protection on selected galleries (add $700+GST)\n(b) Pricing scale on multiple digital purchases in eStore (add $825+GST)\n\n======================================================================', '0.00', 15),
(108, '[ Traffic Starter ] Search Engine Optimisation (SEO)\n- 10 Key phrases \n- Key phrase research \n- Internal website audit \n- Search engine submissions for Google, Yahoo, Bing \n- Monthly ranking reports for Google, Yahoo & Bing \n- Results appear on Google within 90 days \n- Comprehensive onsite optimisation', '500.00', 16),
(109, 'AgentPRO - Individual Agent Website\n\nIndividual agent website, independently hosted at the domain name of your choice (domain name not included) and comes with fixed layout grid that can be coloured and branded as per your corporate identity. Includes 7 pages editable by the Content Management System (WebPublisherCMS) and can easily integrate property listings from your MyDesktop uploads automatically. *Hosting not included and will be delivered within 7-10 business days.', '990.00', 7),
(110, 'WebflowBOS [base system 1-10 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 1-10 Users per month\n*Minimum commitment of 12 months.', '490.00', 18),
(111, 'WebflowBOS [base system 11-30 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 11-30 Users per month\n*Minimum commitment of 12 months.', '690.00', 17),
(112, 'WebflowBOS [base system 31-50 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 31-50 Users per month\n*Minimum commitment of 12 months.', '790.00', 17),
(113, 'WebflowBOS [base system 51-100 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 51-100 Users per month\n*Minimum commitment of 12 months.', '990.00', 17),
(114, 'Accounting System Module (add-on to base system per month) 1-10 Users', '300.00', 17),
(115, 'Accounting System Module (add-on to base system per month) 11-30 Users', '400.00', 17),
(116, 'Accounting System Module (add-on to base system per month) 31-50 Users', '500.00', 17),
(117, 'Accounting System Module (add-on to base system per month) 51-100 Users', '700.00', 17),
(119, 'Unlimited Website Package\n\nStandardised Design Template \nColour Customisation \nHome Page Billboard Slide Show \nContent Management System \nProperty Data Integration (REA/XML) \nBranded Google Maps Email-a-Friend\nPrintable Brochures \nProperty Alerts Property \nQuick Search \nBusiness Hours Training + Support \nUnlimited CMS driven Pages \nInteractive Drop-Down Navigation \nSocial Networking Integration\nAgency BLOG and RSS feeds \nAJAX Property Detail Overlay \nGoogle Analytics\nProperty Search by Google Map\nAdvanced Property Search\nWebsite Keyword Search\nLanguage Translator Google (x3)', '4990.00', 7),
(120, 'PROPERTY CLOUD WEBSITE\nProperty Cloud is a cost-effective way for businesses to get into a world-class property website and benefit from a feature-rich website without the huge price tag. Access to the Property Cloud is billed monthly and includes all the great features outlined in our video demo: http://youtu.be/-91YWtrS9N4', '190.00', 18),
(121, 'CLOUD TERMS\nWe will require 3 weeks of production time to design and develop this project. We will also require an additional 1 week for collaborative testing. Subscription items do not include the provision of or uploading of content to your websites. This is the sole responsibility of the subscriber. You are renting a web service and are under license to use our product for a given and agreed minimum period of time. Failing to meet your monthly installments will result in disruption to your service. Quarterly payments are accepted and a discount is offered of 5% for this payment arrangement.', '0.00', 19),
(125, 'Test item.', '700.00', 21),
(126, 'Quote for the sale, asess land size and so on.', '2000.00', 22),
(128, 'Web Site sale - enterprise of 15 pages, Home page fader, team page, google map, FaceBook integ', '200.00', 18),
(131, 'test description........', '2000.00', 28),
(135, 'Any additional comments Any additional comments Any additional comments Any additional comments Any additional comments ', '300.00', 21),
(136, '24.	Only the task owner has got the rights to re-assign the task to another user.  Any other user who has got the same level of access, will still not be able to re-assign the the tasks.\n25.	Only the task owner has got the rights to re-assign the task to another user.  Any other user who has got the same level of access, will still not be able to re-assign the the tasks.\n', '34.00', 22),
(137, 'Test material', '12.00', 23),
(139, 'park offers several special features not described in this brief tour. For more about features such as Spark Fastpath and Spark plugins, talk to your ..', '400.00', 3),
(143, '\ntest item1', '46.00', 0),
(142, '\ntest item', '23.00', 0),
(144, 'Etiam non ante mi, gravida ultricies mauris. Aliquam gravida pharetra orci ut egestas. Nunc sagittis odio ut libero malesuada scelerisque. Quisque bibendum, libero at blandit aliquet, nunc neque placerat velit, at blandit metus me', '65.23', 4);

-- --------------------------------------------------------

--
-- Table structure for table `crms_client_logo`
--

CREATE TABLE IF NOT EXISTS `crms_client_logo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `client_url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_client_logo`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_contract_jobs`
--

CREATE TABLE IF NOT EXISTS `crms_contract_jobs` (
  `jobid_fk` int(11) NOT NULL,
  `userid_fk` int(11) NOT NULL,
  PRIMARY KEY (`jobid_fk`,`userid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crms_contract_jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_country`
--

CREATE TABLE IF NOT EXISTS `crms_country` (
  `countryid` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(30) NOT NULL,
  `regionid` bigint(20) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` int(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`countryid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_country`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_crons`
--

CREATE TABLE IF NOT EXISTS `crms_crons` (
  `cron_id` int(5) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(250) NOT NULL,
  PRIMARY KEY (`cron_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `crms_crons`
--

INSERT INTO `crms_crons` (`cron_id`, `cron_name`) VALUES
(1, 'Leads - Expected Proposal Date Notification'),
(2, 'Tasks - Task Due Date Notification');

-- --------------------------------------------------------

--
-- Table structure for table `crms_crons_notificatons`
--

CREATE TABLE IF NOT EXISTS `crms_crons_notificatons` (
  `userid` int(5) NOT NULL,
  `cron_id` int(5) NOT NULL,
  `onscreen_notify_status` int(1) NOT NULL,
  `email_notify_status` int(1) NOT NULL,
  `no_of_days` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_crons_notificatons`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_currency_all`
--

CREATE TABLE IF NOT EXISTS `crms_currency_all` (
  `cur_id` int(5) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(150) NOT NULL,
  `cur_name` varchar(150) NOT NULL,
  `cur_short_name` varchar(50) NOT NULL,
  PRIMARY KEY (`cur_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=261 ;

--
-- Dumping data for table `crms_currency_all`
--

INSERT INTO `crms_currency_all` (`cur_id`, `country_name`, `cur_name`, `cur_short_name`) VALUES
(1, 'AFGHANISTAN', 'Afghani', 'AFN'),
(2, 'ALAND ISLANDS', 'Euro', 'EUR'),
(3, 'ALBANIA', 'Lek', 'ALL'),
(4, 'ALGERIA', 'Algerian Dinar', 'DZD'),
(5, 'AMERICAN SAMOA', 'US Dollar', 'USD'),
(6, 'ANDORRA', 'Euro', 'EUR'),
(7, 'ANGOLA', 'Kwanza', 'AOA'),
(8, 'ANGUILLA', 'East Caribbean Dollar', 'XCD'),
(9, 'ANTIGUA AND BARBUDA', 'East Caribbean Dollar', 'XCD'),
(10, 'ARGENTINA', 'Argentine Peso', 'ARS'),
(11, 'ARMENIA', 'Armenian Dram', 'AMD'),
(12, 'ARUBA', 'Aruban Florin', 'AWG'),
(13, 'AUSTRALIA', 'Australian Dollar', 'AUD'),
(14, 'AUSTRIA', 'Euro', 'EUR'),
(15, 'AZERBAIJAN', 'Azerbaijanian Manat', 'AZN'),
(16, 'BAHAMAS', 'Bahamian Dollar', 'BSD'),
(17, 'BAHRAIN', 'Bahraini Dinar', 'BHD'),
(18, 'BANGLADESH', 'Taka', 'BDT'),
(19, 'BARBADOS', 'Barbados Dollar', 'BBD'),
(20, 'BELARUS', 'Belarussian Ruble', 'BYR'),
(21, 'BELGIUM', 'Euro', 'EUR'),
(22, 'BELIZE', 'Belize Dollar', 'BZD'),
(23, 'BENIN', 'CFA Franc BCEAO', 'XOF'),
(24, 'BERMUDA', 'Bermudian Dollar', 'BMD'),
(25, 'BHUTAN', 'Ngultrum', 'BTN'),
(26, 'BONAIRE, SINT EUSTATIUS AND SABA', 'US Dollar', 'USD'),
(27, 'BOSNIA AND HERZEGOVINA', 'Convertible Mark', 'BAM'),
(28, 'BOTSWANA', 'Pula', 'BWP'),
(29, 'BOUVET ISLAND', 'Norwegian Krone', 'NOK'),
(30, 'BRAZIL', 'Brazilian Real', 'BRL'),
(31, 'BRITISH INDIAN OCEAN TERRITORY', 'US Dollar', 'USD'),
(32, 'BRUNEI DARUSSALAM', 'Brunei Dollar', 'BND'),
(33, 'BULGARIA', 'Bulgarian Lev', 'BGN'),
(34, 'BURKINA FASO', 'CFA Franc BCEAO', 'XOF'),
(35, 'BURUNDI', 'Burundi Franc', 'BIF'),
(36, 'CAMBODIA', 'Riel', 'KHR'),
(37, 'CAMEROON', 'CFA Franc BEAC', 'XAF'),
(38, 'CANADA', 'Canadian Dollar', 'CAD'),
(39, 'CAPE VERDE', 'Cape Verde Escudo', 'CVE'),
(40, 'CAYMAN ISLANDS', 'Cayman Islands Dollar', 'KYD'),
(41, 'CENTRAL AFRICAN REPUBLIC', 'CFA Franc BEAC', 'XAF'),
(42, 'CHAD', 'CFA Franc BEAC', 'XAF'),
(43, 'CHILE', 'Unidades de fomento', 'CLF'),
(44, 'CHILE', 'Chilean Peso', 'CLP'),
(45, 'CHINA', 'Yuan Renminbi', 'CNY'),
(46, 'CHRISTMAS ISLAND', 'Australian Dollar', 'AUD'),
(47, 'COCOS (KEELING) ISLANDS', 'Australian Dollar', 'AUD'),
(48, 'COLOMBIA', 'Colombian Peso', 'COP'),
(49, 'COLOMBIA', 'Unidad de Valor Real', 'COU'),
(50, 'COMOROS', 'Comoro Franc', 'KMF'),
(51, 'CONGO', 'CFA Franc BEAC', 'XAF'),
(52, 'CONGO, THE DEMOCRATIC REPUBLIC OF', 'Congolese Franc', 'CDF'),
(53, 'COOK ISLANDS', 'New Zealand Dollar', 'NZD'),
(54, 'COSTA RICA', 'Costa Rican Colon', 'CRC'),
(55, 'COTE D''IVOIRE', 'CFA Franc BCEAO', 'XOF'),
(56, 'CROATIA', 'Croatian Kuna', 'HRK'),
(57, 'CUBA', 'Peso Convertible', 'CUC'),
(58, 'CUBA', 'Cuban Peso', 'CUP'),
(59, 'CURACAO', 'Netherlands Antillean Guilder', 'ANG'),
(60, 'CYPRUS', 'Euro', 'EUR'),
(61, 'CZECH REPUBLIC', 'Czech Koruna', 'CZK'),
(62, 'DENMARK', 'Danish Krone', 'DKK'),
(63, 'DJIBOUTI', 'Djibouti Franc', 'DJF'),
(64, 'DOMINICA', 'East Caribbean Dollar', 'XCD'),
(65, 'DOMINICAN REPUBLIC', 'Dominican Peso', 'DOP'),
(66, 'ECUADOR', 'US Dollar', 'USD'),
(67, 'EGYPT', 'Egyptian Pound', 'EGP'),
(68, 'EL SALVADOR', 'El Salvador Colon', 'SVC'),
(69, 'EL SALVADOR', 'US Dollar', 'USD'),
(70, 'EQUATORIAL GUINEA', 'CFA Franc BEAC', 'XAF'),
(71, 'ERITREA', 'Nakfa', 'ERN'),
(72, 'ESTONIA', 'Euro', 'EUR'),
(73, 'ETHIOPIA', 'Ethiopian Birr', 'ETB'),
(74, 'EUROPEAN UNION', 'Euro', 'EUR'),
(75, 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands Pound', 'FKP'),
(76, 'FAROE ISLANDS', 'Danish Krone', 'DKK'),
(77, 'FIJI', 'Fiji Dollar', 'FJD'),
(78, 'FINLAND', 'Euro', 'EUR'),
(79, 'FRANCE', 'Euro', 'EUR'),
(80, 'FRENCH GUIANA', 'Euro', 'EUR'),
(81, 'FRENCH POLYNESIA', 'CFP Franc', 'XPF'),
(82, 'FRENCH SOUTHERN TERRITORIES', 'Euro', 'EUR'),
(83, 'GABON', 'CFA Franc BEAC', 'XAF'),
(84, 'GAMBIA', 'Dalasi', 'GMD'),
(85, 'GEORGIA', 'Lari', 'GEL'),
(86, 'GERMANY', 'Euro', 'EUR'),
(87, 'GHANA', 'Ghana Cedi', 'GHS'),
(88, 'GIBRALTAR', 'Gibraltar Pound', 'GIP'),
(89, 'GREECE', 'Euro', 'EUR'),
(90, 'GREENLAND', 'Danish Krone', 'DKK'),
(91, 'GRENADA', 'East Caribbean Dollar', 'XCD'),
(92, 'GUADELOUPE', 'Euro', 'EUR'),
(93, 'GUAM', 'US Dollar', 'USD'),
(94, 'GUATEMALA', 'Quetzal', 'GTQ'),
(95, 'GUERNSEY', 'Pound Sterling', 'GBP'),
(96, 'GUINEA', 'Guinea Franc', 'GNF'),
(97, 'GUINEA-BISSAU', 'CFA Franc BCEAO', 'XOF'),
(98, 'GUYANA', 'Guyana Dollar', 'GYD'),
(99, 'HAITI', 'Gourde', 'HTG'),
(100, 'HAITI', 'US Dollar', 'USD'),
(101, 'HEARD ISLAND AND McDONALD ISLANDS', 'Australian Dollar', 'AUD'),
(102, 'HOLY SEE (VATICAN CITY STATE)', 'Euro', 'EUR'),
(103, 'HONDURAS', 'Lempira', 'HNL'),
(104, 'HONG KONG', 'Hong Kong Dollar', 'HKD'),
(105, 'HUNGARY', 'Forint', 'HUF'),
(106, 'ICELAND', 'Iceland Krona', 'ISK'),
(107, 'INDIA', 'Indian Rupee', 'INR'),
(108, 'INDONESIA', 'Rupiah', 'IDR'),
(109, 'IRAN, ISLAMIC REPUBLIC OF', 'Iranian Rial', 'IRR'),
(110, 'IRAQ', 'Iraqi Dinar', 'IQD'),
(111, 'IRELAND', 'Euro', 'EUR'),
(112, 'ISLE OF MAN', 'Pound Sterling', 'GBP'),
(113, 'ISRAEL', 'New Israeli Sheqel', 'ILS'),
(114, 'ITALY', 'Euro', 'EUR'),
(115, 'JAMAICA', 'Jamaican Dollar', 'JMD'),
(116, 'JAPAN', 'Yen', 'JPY'),
(117, 'JERSEY', 'Pound Sterling', 'GBP'),
(118, 'JORDAN', 'Jordanian Dinar', 'JOD'),
(119, 'KAZAKHSTAN', 'Tenge', 'KZT'),
(120, 'KENYA', 'Kenyan Shilling', 'KES'),
(121, 'KIRIBATI', 'Australian Dollar', 'AUD'),
(122, 'KOREA, DEMOCRATIC PEOPLE', 'North Korean Won', 'KPW'),
(123, 'KOREA, REPUBLIC OF', 'Won', 'KRW'),
(124, 'KUWAIT', 'Kuwaiti Dinar', 'KWD'),
(125, 'KYRGYZSTAN', 'Som', 'KGS'),
(126, 'LAO PEOPLE', 'Kip', 'LAK'),
(127, 'LATVIA', 'Latvian Lats', 'LVL'),
(128, 'LEBANON', 'Lebanese Pound', 'LBP'),
(129, 'LESOTHO', 'Loti', 'LSL'),
(130, 'LESOTHO', 'Rand', 'ZAR'),
(131, 'LIBERIA', 'Liberian Dollar', 'LRD'),
(132, 'LIBYA', 'Libyan Dinar', 'LYD'),
(133, 'LIECHTENSTEIN', 'Swiss Franc', 'CHF'),
(134, 'LITHUANIA', 'Lithuanian Litas', 'LTL'),
(135, 'LUXEMBOURG', 'Euro', 'EUR'),
(136, 'MACAO', 'Pataca', 'MOP'),
(137, 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Denar', 'MKD'),
(138, 'MADAGASCAR', 'Malagasy Ariary', 'MGA'),
(139, 'MALAWI', 'Kwacha', 'MWK'),
(140, 'MALAYSIA', 'Malaysian Ringgit', 'MYR'),
(141, 'MALDIVES', 'Rufiyaa', 'MVR'),
(142, 'MALI', 'CFA Franc BCEAO', 'XOF'),
(143, 'MALTA', 'Euro', 'EUR'),
(144, 'MARSHALL ISLANDS', 'US Dollar', 'USD'),
(145, 'MARTINIQUE', 'Euro', 'EUR'),
(146, 'MAURITANIA', 'Ouguiya', 'MRO'),
(147, 'MAURITIUS', 'Mauritius Rupee', 'MUR'),
(148, 'MAYOTTE', 'Euro', 'EUR'),
(149, 'MEMBER COUNTRIES OF THE AFRICAN DEVELOPMENT BANK GROUP', 'ADB Unit of Account', 'XUA'),
(150, 'MEXICO', 'Mexican Peso', 'MXN'),
(151, 'MEXICO', 'Mexican Unidad de Inversion (UDI)', 'MXV'),
(152, 'MICRONESIA, FEDERATED STATES OF', 'US Dollar', 'USD'),
(153, 'MOLDOVA, REPUBLIC OF', 'Moldovan Leu', 'MDL'),
(154, 'MONACO', 'Euro', 'EUR'),
(155, 'MONGOLIA', 'Tugrik', 'MNT'),
(156, 'MONTENEGRO', 'Euro', 'EUR'),
(157, 'MONTSERRAT', 'East Caribbean Dollar', 'XCD'),
(158, 'MOROCCO', 'Moroccan Dirham', 'MAD'),
(159, 'MOZAMBIQUE', 'Mozambique Metical', 'MZN'),
(160, 'MYANMAR', 'Kyat', 'MMK'),
(161, 'NAMIBIA', 'Namibia Dollar', 'NAD'),
(162, 'NAMIBIA', 'Rand', 'ZAR'),
(163, 'NAURU', 'Australian Dollar', 'AUD'),
(164, 'NEPAL', 'Nepalese Rupee', 'NPR'),
(165, 'NETHERLANDS', 'Euro', 'EUR'),
(166, 'NEW CALEDONIA', 'CFP Franc', 'XPF'),
(167, 'NEW ZEALAND', 'New Zealand Dollar', 'NZD'),
(168, 'NICARAGUA', 'Cordoba Oro', 'NIO'),
(169, 'NIGER', 'CFA Franc BCEAO', 'XOF'),
(170, 'NIGERIA', 'Naira', 'NGN'),
(171, 'NIUE', 'New Zealand Dollar', 'NZD'),
(172, 'NORFOLK ISLAND', 'Australian Dollar', 'AUD'),
(173, 'NORTHERN MARIANA ISLANDS', 'US Dollar', 'USD'),
(174, 'NORWAY', 'Norwegian Krone', 'NOK'),
(175, 'OMAN', 'Rial Omani', 'OMR'),
(176, 'PAKISTAN', 'Pakistan Rupee', 'PKR'),
(177, 'PALAU', 'US Dollar', 'USD'),
(178, 'PANAMA', 'Balboa', 'PAB'),
(179, 'PANAMA', 'US Dollar', 'USD'),
(180, 'PAPUA NEW GUINEA', 'Kina', 'PGK'),
(181, 'PARAGUAY', 'Guarani', 'PYG'),
(182, 'PERU', 'Nuevo Sol', 'PEN'),
(183, 'PHILIPPINES', 'Philippine Peso', 'PHP'),
(184, 'PITCAIRN', 'New Zealand Dollar', 'NZD'),
(185, 'POLAND', 'Zloty', 'PLN'),
(186, 'PORTUGAL', 'Euro', 'EUR'),
(187, 'PUERTO RICO', 'US Dollar', 'USD'),
(188, 'QATAR', 'Qatari Rial', 'QAR'),
(189, 'REUNION', 'Euro', 'EUR'),
(190, 'ROMANIA', 'New Romanian Leu', 'RON'),
(191, 'RUSSIAN FEDERATION', 'Russian Ruble', 'RUB'),
(192, 'RWANDA', 'Rwanda Franc', 'RWF'),
(193, 'SAINT BARTHELEMY', 'Euro', 'EUR'),
(194, 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA', 'Saint Helena Pound', 'SHP'),
(195, 'SAINT KITTS AND NEVIS', 'East Caribbean Dollar', 'XCD'),
(196, 'SAINT LUCIA', 'East Caribbean Dollar', 'XCD'),
(197, 'SAINT MARTIN (FRENCH PART)', 'Euro', 'EUR'),
(198, 'SAINT PIERRE AND MIQUELON', 'Euro', 'EUR'),
(199, 'SAINT VINCENT AND THE GRENADINES', 'East Caribbean Dollar', 'XCD'),
(200, 'SAMOA', 'Tala', 'WST'),
(201, 'SAN MARINO', 'Euro', 'EUR'),
(202, 'SAO TOME AND PRINCIPE', 'Dobra', 'STD'),
(203, 'SAUDI ARABIA', 'Saudi Riyal', 'SAR'),
(204, 'SENEGAL', 'CFA Franc BCEAO', 'XOF'),
(205, 'SERBIA', 'Serbian Dinar', 'RSD'),
(206, 'SEYCHELLES', 'Seychelles Rupee', 'SCR'),
(207, 'SIERRA LEONE', 'Leone', 'SLL'),
(208, 'SINGAPORE', 'Singapore Dollar', 'SGD'),
(209, 'SINT MAARTEN (DUTCH PART)', 'Netherlands Antillean Guilder', 'ANG'),
(210, 'SLOVAKIA', 'Euro', 'EUR'),
(211, 'SLOVENIA', 'Euro', 'EUR'),
(212, 'SOLOMON ISLANDS', 'Solomon Islands Dollar', 'SBD'),
(213, 'SOMALIA', 'Somali Shilling', 'SOS'),
(214, 'SOUTH AFRICA', 'Rand', 'ZAR'),
(215, 'SOUTH SUDAN', 'South Sudanese Pound', 'SSP'),
(216, 'SPAIN', 'Euro', 'EUR'),
(217, 'SRI LANKA', 'Sri Lanka Rupee', 'LKR'),
(218, 'SUDAN', 'Sudanese Pound', 'SDG'),
(219, 'SURINAME', 'Surinam Dollar', 'SRD'),
(220, 'SVALBARD AND JAN MAYEN', 'Norwegian Krone', 'NOK'),
(221, 'SWAZILAND', 'Lilangeni', 'SZL'),
(222, 'SWEDEN', 'Swedish Krona', 'SEK'),
(223, 'SWITZERLAND', 'WIR Euro', 'CHE'),
(224, 'SWITZERLAND', 'Swiss Franc', 'CHF'),
(225, 'SWITZERLAND', 'WIR Franc', 'CHW'),
(226, 'SYRIAN ARAB REPUBLIC', 'Syrian Pound', 'SYP'),
(227, 'TAIWAN, PROVINCE OF CHINA', 'New Taiwan Dollar', 'TWD'),
(228, 'TAJIKISTAN', 'Somoni', 'TJS'),
(229, 'TANZANIA, UNITED REPUBLIC OF', 'Tanzanian Shilling', 'TZS'),
(230, 'THAILAND', 'Baht', 'THB'),
(231, 'TIMOR-LESTE', 'US Dollar', 'USD'),
(232, 'TOGO', 'CFA Franc BCEAO', 'XOF'),
(233, 'TOKELAU', 'New Zealand Dollar', 'NZD'),
(234, 'TONGA', 'Pa', 'TOP'),
(235, 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago Dollar', 'TTD'),
(236, 'TUNISIA', 'Tunisian Dinar', 'TND'),
(237, 'TURKEY', 'Turkish Lira', 'TRY'),
(238, 'TURKMENISTAN', 'Turkmenistan New Manat', 'TMT'),
(239, 'TURKS AND CAICOS ISLANDS', 'US Dollar', 'USD'),
(240, 'TUVALU', 'Australian Dollar', 'AUD'),
(241, 'UGANDA', 'Uganda Shilling', 'UGX'),
(242, 'UKRAINE', 'Hryvnia', 'UAH'),
(243, 'UNITED ARAB EMIRATES', 'UAE Dirham', 'AED'),
(244, 'UNITED KINGDOM', 'Pound Sterling', 'GBP'),
(245, 'UNITED STATES', 'US Dollar', 'USD'),
(246, 'UNITED STATES MINOR OUTLYING ISLANDS', 'US Dollar', 'USD'),
(247, 'URUGUAY', 'Uruguay Peso en Unidades Indexadas (URUIURUI)', 'UYI'),
(248, 'URUGUAY', 'Peso Uruguayo', 'UYU'),
(249, 'UZBEKISTAN', 'Uzbekistan Sum', 'UZS'),
(250, 'VANUATU', 'Vatu', 'VUV'),
(251, 'Vatican City State (HOLY SEE)', 'Euro', 'EUR'),
(252, 'VENEZUELA, BOLIVARIAN REPUBLIC OF', 'Bolivar', 'VEF'),
(253, 'VIET NAM', 'Dong', 'VND'),
(254, 'VIRGIN ISLANDS (BRITISH)', 'US Dollar', 'USD'),
(255, 'VIRGIN ISLANDS (US)', 'US Dollar', 'USD'),
(256, 'WALLIS AND FUTUNA', 'CFP Franc', 'XPF'),
(257, 'WESTERN SAHARA', 'Moroccan Dirham', 'MAD'),
(258, 'YEMEN', 'Yemeni Rial', 'YER'),
(259, 'ZAMBIA', 'Zambian Kwacha', 'ZMW'),
(260, 'ZIMBABWE', 'Zimbabwe Dollar', 'ZWL');

-- --------------------------------------------------------

--
-- Table structure for table `crms_currency_rate`
--

CREATE TABLE IF NOT EXISTS `crms_currency_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` double NOT NULL,
  `to` double NOT NULL,
  `value` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_currency_rate`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_customers`
--

CREATE TABLE IF NOT EXISTS `crms_customers` (
  `custid` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `abn` varchar(20) DEFAULT NULL,
  `add1_line1` varchar(150) DEFAULT NULL,
  `add1_line2` varchar(150) DEFAULT NULL,
  `add1_suburb` varchar(100) DEFAULT NULL,
  `add1_region` varchar(64) DEFAULT NULL,
  `add1_country` varchar(64) DEFAULT NULL,
  `add1_state` varchar(4) DEFAULT NULL,
  `add1_location` varchar(64) DEFAULT NULL,
  `add1_postcode` varchar(10) DEFAULT NULL,
  `phone_1` varchar(30) DEFAULT NULL,
  `phone_2` varchar(30) DEFAULT NULL,
  `phone_3` varchar(30) DEFAULT NULL,
  `phone_4` varchar(30) DEFAULT NULL,
  `email_1` varchar(200) DEFAULT NULL,
  `email_2` varchar(200) DEFAULT NULL,
  `email_3` varchar(200) DEFAULT NULL,
  `email_4` varchar(200) DEFAULT NULL,
  `www_1` varchar(200) DEFAULT NULL,
  `www_2` varchar(200) DEFAULT NULL,
  `comments` text,
  `exported` datetime DEFAULT NULL,
  `skype_name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`custid`),
  FULLTEXT KEY `first_name` (`first_name`,`last_name`,`company`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_customers`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_customer_categories`
--

CREATE TABLE IF NOT EXISTS `crms_customer_categories` (
  `custcatid` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `cat_comments` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`custcatid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_customer_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_customer_notify`
--

CREATE TABLE IF NOT EXISTS `crms_customer_notify` (
  `name` varchar(200) NOT NULL,
  `email` varchar(250) NOT NULL,
  `notification` tinyint(4) NOT NULL DEFAULT '1',
  `sent` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_customer_notify`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_deposits`
--

CREATE TABLE IF NOT EXISTS `crms_deposits` (
  `depositid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `amount` decimal(7,2) NOT NULL,
  `deposit_date` datetime NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `userid_fk` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `payment_received` int(11) NOT NULL,
  `map_term` varchar(250) NOT NULL,
  PRIMARY KEY (`depositid`),
  KEY `jobid_fk` (`jobid_fk`),
  KEY `userid_fk` (`userid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_deposits`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_dns`
--

CREATE TABLE IF NOT EXISTS `crms_dns` (
  `hostingid` int(11) NOT NULL,
  `host_location` varchar(250) NOT NULL,
  `login_url` varchar(250) NOT NULL,
  `login` varchar(150) NOT NULL,
  `registrar_password` varchar(40) NOT NULL,
  `tech_contact` varchar(250) NOT NULL,
  `tech_email` varchar(250) NOT NULL,
  `tech_name` varchar(250) NOT NULL,
  `client_contact` varchar(250) NOT NULL,
  `client_email` varchar(250) NOT NULL,
  `client_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `go_live_date` date NOT NULL,
  `email_change` varchar(5) NOT NULL,
  `cur_smtp_setting` varchar(250) NOT NULL,
  `cur_pop_setting` varchar(250) NOT NULL,
  `cur_webmail_url` varchar(250) NOT NULL,
  `cur_controlpanel_url` varchar(250) NOT NULL,
  `cur_statspanel_url` varchar(250) NOT NULL,
  `cur_dns_primary_url` varchar(250) NOT NULL,
  `cur_dns_primary_ip` varchar(250) NOT NULL,
  `cur_dns_secondary_url` varchar(250) NOT NULL,
  `cur_dns_secondary_ip` varchar(250) NOT NULL,
  `cur_record_setting` varchar(250) NOT NULL,
  `cur_mx_record` varchar(250) NOT NULL,
  `fut_smtp_setting` varchar(250) NOT NULL,
  `fut_pop_setting` varchar(250) NOT NULL,
  `fut_webmail_url` varchar(250) NOT NULL,
  `fut_controlpanel_url` varchar(250) NOT NULL,
  `fut_statspanel_url` varchar(250) NOT NULL,
  `fut_dns_primary_url` varchar(250) NOT NULL,
  `fut_dns_primary_ip` varchar(250) NOT NULL,
  `fut_dns_secondary_url` varchar(250) NOT NULL,
  `fut_dns_secondary_ip` varchar(250) NOT NULL,
  `fut_record_setting` varchar(250) NOT NULL,
  `fut_mx_record` varchar(250) NOT NULL,
  `date_handover` date NOT NULL,
  `host_status` int(11) NOT NULL,
  PRIMARY KEY (`hostingid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_dns`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_email_template`
--

CREATE TABLE IF NOT EXISTS `crms_email_template` (
  `email_tempid` int(11) NOT NULL AUTO_INCREMENT,
  `email_templatename` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email_templatesubject` text CHARACTER SET utf8 NOT NULL,
  `email_templatefrom` varchar(100) CHARACTER SET utf8 NOT NULL,
  `email_templatecontent` text CHARACTER SET utf8 NOT NULL,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_tempid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `crms_email_template`
--

INSERT INTO `crms_email_template` (`email_tempid`, `email_templatename`, `email_templatesubject`, `email_templatefrom`, `email_templatecontent`, `modified_on`) VALUES
(1, 'Lead Notificatiion Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n    <td>\r\n	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n    <p style="background: none repeat scroll 0 0 #4B6FB9;\r\n    border-bottom: 1px solid #CCCCCC;\r\n    color: #FFFFFF;\r\n    margin: 0;\r\n    padding: 4px;">\r\n        <span>{{print_fancydate}}</span>   {{first_name}} {{last_name}}</p>\r\n    <p style="padding: 4px;">{{log_content}}<br /><br />\r\n		This log has been emailed to:<br />\r\n		{{received_by}}<br /><br />\r\n		{{signature}}<br />\r\n    </p>\r\n</div>\r\n</td>\r\n  </tr>\r\n\r\n   <tr>\r\n    <td> </td>\r\n  </tr>', '2013-11-06 09:52:03'),
(2, 'New Lead Creation Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Lead Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr><td><table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="96%" align="center" cellspacing="0" cellpadding="4"><tr><p><td style="border-right:1px #CCC solid; color:#FFF"" width="73" bgcolor="#4B6FB9"><b>Title</b></td><td  style="border-right:1px #CCC solid; color:#FFF""width="41" bgcolor="#4B6FB9"><b>Description</b></td>\r\n</p></tr><tr><td style="border-right:1px #CCC solid;">Client</td><td style="border-right:1px #CCC solid;">{{first_name}} {{last_name}}-{{company}}</td></tr>\r\n<tr style="border:1px #CCC solid;"><td style="border-right:1px #CCC solid;">URL</td><td style="border-right:1px #CCC solid;"><a href="{{base_url}}welcome/view_quote/{{insert_id}}">Click here to View Lead</a></td></tr></table></td></tr>', '2013-11-06 09:52:11'),
(3, 'Lead Re-assignment Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Re-assignment Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n	<td>\r\n		<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n		<p style="background: none repeat scroll 0 0 #4B6FB9;\r\n		border-bottom: 1px solid #CCCCCC;\r\n		color: #FFFFFF;\r\n		margin: 0;\r\n		padding: 4px;">\r\n			<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n			<p style="padding: 4px;">{{log_content}}<br /><br />\r\n				{{signature}}<br />\r\n			</p>\r\n		</div>\r\n	</td>\r\n</tr>', '2013-11-06 09:52:18'),
(4, 'Lead Owner Re-assignment Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Owner Re-assignment Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:25'),
(5, 'Lead - Status Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Status Change Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content_email}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:32'),
(6, 'Lead - Delete Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Deleted Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:39'),
(7, 'Lead to Project Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead to Project Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content_email}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:46'),
(8, 'New Customer Creation', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Customer Creation Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">New Customer Created -{{first_name}}  {{last_name}} - {{company}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 10:47:36'),
(9, 'Customer Details Modification Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Customer Details Modification Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>   {{user_name}}</p>\r\n<p style="padding: 4px;">Customer Details Modified - {{first_name}}  {{last_name}} - {{company}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 10:49:57'),
(10, 'User Profile Changes Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Profile Changes Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>   {{user_name}}</p>\r\n<p style="padding: 4px;">User Profile Modified - {{first_name}}  {{last_name}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 11:23:21'),
(11, 'New User Creation Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New User Creation Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">New user Created - {{first_name}}  {{last_name}} <br /><br />\r\nUser Login Details<br /><br />\r\nLogin URL : {{base_url}} <br />User Login email id : {{email}}<br />\r\nPassword : {{password}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 11:51:46'),
(12, 'User Level Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Level Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;"> User Level has been Changed for - {{first_name}}  {{last_name}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 12:01:57'),
(13, 'User Role Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Role Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;"> User Role has been Changed for - {{first_name}}  {{last_name}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 12:06:16'),
(14, 'Project Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{first_name}}  {{last_name}}</p>\r\n<p style="padding: 4px;"> {{log_content}} <br /><br />\r\nThis log has been emailed to:<br />\r\n{{received_by}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 13:46:16'),
(15, 'Project Assignment / Removal Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>\r\n</p>\r\n<p style="padding: 4px;"> \r\n<span>Hi </span>&nbsp;{{first_name}},</p>\r\n<span>{{email_content}}</span><br /><br />\r\n<span>Regards,</span><br />\r\n<span>Webmaster</span>\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 14:46:12'),
(16, 'Assign / Remove Project Members', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>\r\n</p>\r\n<p style="padding: 4px;">\r\n<span>Hi </span> {{first_name}},<br />\r\n<br />\r\n<span> {{log_email_content}}</span><br /><br />\r\n<span> Regards,</span><br />\r\n<span> Webmaster</span>\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 15:04:52'),
(17, 'Task Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" style="border:1px solid #666666;border-collapse: collapse;">\r\n<tbody><tr>\r\n<td width="80" valign="top" style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask Desc\r\n</td>\r\n<td colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{task_name}}\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;" >\r\nRemarks\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px; ">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1;">\r\nStatus\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n{{task_status}} %\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 18:37:29'),
(18, 'Task Delete Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Delete Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>   </p>\r\n<p style="padding: 4px;">{{task_name}}  has been declined by {{user_name}}<br /><br />{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 16:39:45'),
(19, 'Task Completion Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Completion Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;\r\nborder-collapse: collapse;">\r\n<tbody>\r\n<tr>\r\n<td width="80" valign="top" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask\r\n</td>\r\n<td class="task" colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{task_name}}\r\n</td>\r\n</tr>\r\n<tr>\r\n<td  style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nRemarks\r\n</td>\r\n<td rel="2:0" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated&nbsp;by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nStatus\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n{{task_status}}\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 17:00:54'),
(20, 'New Task Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Task Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;\r\nborder-collapse: collapse;">\r\n<tbody>\r\n<tr>\r\n<td width="80" valign="top" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask Desc\r\n</td>\r\n<td class="task" colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{job_task}}</a>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;" >\r\nRemarks\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nStatus\r\n</td>\r\n<td <td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n  {{status}} %\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 18:15:42'),
(21, 'Task Update Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Update Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" align="center" cellpadding="0" border="0" style="border:1px solid #666666;\r\nborder-collapse: collapse;">\r\n<tbody><tr>\r\n<td width="80" valign="top" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask\r\n</td>\r\n<td class="task" colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{job_task}}\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{taskAssignedTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;" >\r\nRemarks\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated&nbsp;by:\r\n</td>\r\n<td width="100" width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nStatus\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n{{status}} %\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 17:50:40');

-- --------------------------------------------------------

--
-- Table structure for table `crms_email_template_hf`
--

CREATE TABLE IF NOT EXISTS `crms_email_template_hf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_template_header` text NOT NULL,
  `email_template_footer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `crms_email_template_hf`
--

INSERT INTO `crms_email_template_hf` (`id`, `email_template_header`, `email_template_footer`) VALUES
(1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml">\r\n<head>\r\n<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />\r\n<title>Email Template</title>\r\n<style type="text/css">\r\nbody {\r\n	margin: 0px;\r\n}\r\n</style>\r\n</head>\r\n\r\n<body>\r\n<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">\r\n<tr><td bgcolor="#FFFFFF">\r\n<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">\r\n  <tr>\r\n    <td style="padding:15px; border-bottom:2px #5a595e solid;">\r\n		<img src="http://crm.enoahisolution.com/assets/img/esmart_logo.jpg" />\r\n	</td>\r\n  </tr>', '<tr>\r\n<td> </td>\r\n</tr>\r\n<tr>\r\n    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>\r\n  </tr>\r\n</table>\r\n</td>\r\n</tr>\r\n</table>\r\n</body>\r\n</html>');

-- --------------------------------------------------------

--
-- Table structure for table `crms_expected_payments`
--

CREATE TABLE IF NOT EXISTS `crms_expected_payments` (
  `expectid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `amount` decimal(7,2) NOT NULL,
  `expected_date` datetime NOT NULL,
  `received` int(11) NOT NULL DEFAULT '0',
  `comments` varchar(255) DEFAULT NULL,
  `project_milestone_name` varchar(255) NOT NULL,
  PRIMARY KEY (`expectid`),
  KEY `jobid_fk` (`jobid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_expected_payments`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_expect_worth`
--

CREATE TABLE IF NOT EXISTS `crms_expect_worth` (
  `expect_worth_id` int(4) NOT NULL AUTO_INCREMENT,
  `expect_worth_name` varchar(20) NOT NULL,
  `cur_name` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `is_default` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`expect_worth_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `crms_expect_worth`
--

INSERT INTO `crms_expect_worth` (`expect_worth_id`, `expect_worth_name`, `cur_name`, `status`, `is_default`) VALUES
(1, 'USD', 'US Dollar', 1, 0),
(2, 'AUD', 'Australian Dollar', 1, 0),
(3, 'SGD', 'Singapore Dollar', 1, 0),
(4, 'MYR', 'Malaysian Ringgit', 1, 0),
(5, 'INR', 'Indian Rupee', 1, 1),
(6, 'EUR', 'Euro', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_hosting`
--

CREATE TABLE IF NOT EXISTS `crms_hosting` (
  `hostingid` int(11) NOT NULL AUTO_INCREMENT,
  `custid_fk` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `domain_status` tinyint(4) NOT NULL DEFAULT '0',
  `expiry_date` date NOT NULL,
  `ssl` int(11) NOT NULL DEFAULT '0',
  `domain_expiry` date DEFAULT NULL,
  `other_info` text,
  PRIMARY KEY (`hostingid`),
  UNIQUE KEY `domain_name` (`domain_name`),
  KEY `custid_fk` (`custid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_hosting`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_hosting_package`
--

CREATE TABLE IF NOT EXISTS `crms_hosting_package` (
  `hostingid_fk` int(11) NOT NULL,
  `packageid_fk` int(11) NOT NULL,
  `due_date` date NOT NULL,
  KEY `hostingid_fk` (`hostingid_fk`,`packageid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_hosting_package`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_items`
--

CREATE TABLE IF NOT EXISTS `crms_items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `item_position` int(11) NOT NULL DEFAULT '0',
  `item_desc` text,
  `item_price` decimal(9,2) DEFAULT NULL,
  `hours` decimal(5,2) DEFAULT NULL,
  `ledger_code` varchar(10) NOT NULL DEFAULT '41000',
  PRIMARY KEY (`itemid`),
  KEY `jobid_fk` (`jobid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_job_categories`
--

CREATE TABLE IF NOT EXISTS `crms_job_categories` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `crms_job_categories`
--

INSERT INTO `crms_job_categories` (`cid`, `category`, `status`) VALUES
(1, 'eConnect', 1),
(10, 'e-Stone', 1),
(2, 'e-CRM', 1),
(3, 'iFlow', 1),
(4, 'SEO Services', 1),
(5, 'Web Hosting', 1),
(7, '.NET Development', 1),
(8, 'PHP Development', 1),
(9, 'Domain Registration', 1),
(11, 'eCommerce Portal', 1),
(12, 'Recruitment Services', 1),
(13, 'Contract Staffing', 1),
(14, 'SAP Opportunity', 1),
(39, 'BPO Services', 1),
(40, 'Others', 1),
(47, 'Mobilty Services', 1);

-- --------------------------------------------------------

--
-- Table structure for table `crms_job_urls`
--

CREATE TABLE IF NOT EXISTS `crms_job_urls` (
  `urlid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `userid_fk` int(11) NOT NULL,
  `url` tinytext NOT NULL,
  `content` text,
  `date` datetime NOT NULL,
  PRIMARY KEY (`urlid`),
  KEY `jobid_fk` (`jobid_fk`),
  KEY `userid_fk` (`userid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_job_urls`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_leads`
--

CREATE TABLE IF NOT EXISTS `crms_leads` (
  `jobid` int(11) NOT NULL AUTO_INCREMENT,
  `job_title` varchar(200) NOT NULL,
  `job_desc` text,
  `job_category` tinyint(4) DEFAULT NULL,
  `lead_source` int(5) DEFAULT NULL,
  `lead_assign` int(5) DEFAULT NULL,
  `expect_worth_id` int(4) NOT NULL,
  `expect_worth_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `actual_worth_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `invoice_no` varchar(40) DEFAULT NULL,
  `custid_fk` int(11) NOT NULL,
  `date_quoted` datetime DEFAULT NULL,
  `date_invoiced` datetime DEFAULT NULL,
  `job_status` tinyint(4) DEFAULT '1',
  `complete_status` tinyint(4) DEFAULT NULL,
  `assigned_to` int(4) DEFAULT NULL,
  `pjt_id` varchar(20) DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_due` datetime DEFAULT NULL,
  `actual_date_start` datetime DEFAULT NULL,
  `actual_date_due` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `proposal_expected_date` datetime DEFAULT NULL,
  `proposal_adjusted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(5) DEFAULT NULL,
  `account_manager` int(11) DEFAULT NULL,
  `belong_to` varchar(10) NOT NULL DEFAULT 'VT',
  `division` varchar(10) NOT NULL DEFAULT 'VTD',
  `payment_terms` int(11) NOT NULL DEFAULT '0',
  `log_view_status` varchar(200) DEFAULT NULL,
  `lead_status` int(1) NOT NULL DEFAULT '1',
  `pjt_status` int(1) NOT NULL DEFAULT '0',
  `lead_indicator` varchar(32) DEFAULT NULL,
  `lead_hold_reason` text,
  PRIMARY KEY (`jobid`),
  KEY `custid_fk` (`custid_fk`),
  KEY `assigned_to` (`assigned_to`),
  KEY `belong_to` (`belong_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_leads`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_files`
--

CREATE TABLE IF NOT EXISTS `crms_lead_files` (
  `lead_files_name` text NOT NULL,
  `lead_files_created_by` int(4) NOT NULL,
  `lead_files_created_on` datetime NOT NULL,
  `jobid` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_query`
--

CREATE TABLE IF NOT EXISTS `crms_lead_query` (
  `query_id` int(5) NOT NULL AUTO_INCREMENT,
  `job_id` int(16) NOT NULL,
  `user_id` int(5) NOT NULL,
  `query_msg` varchar(1024) NOT NULL,
  `query_file_name` varchar(255) NOT NULL,
  `query_sent_date` datetime NOT NULL,
  `query_sent_to` varchar(255) NOT NULL,
  `query_from` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `replay_query` int(5) NOT NULL,
  PRIMARY KEY (`query_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_lead_query`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_source`
--

CREATE TABLE IF NOT EXISTS `crms_lead_source` (
  `lead_source_id` int(5) NOT NULL AUTO_INCREMENT,
  `lead_source_name` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`lead_source_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=12 ;

--
-- Dumping data for table `crms_lead_source`
--

INSERT INTO `crms_lead_source` (`lead_source_id`, `lead_source_name`, `status`) VALUES
(1, 'Tele-calling', 1),
(2, 'Direct Field Visit', 1),
(3, 'Existing Client Reference', 1),
(4, 'Known Circle Reference', 1),
(5, 'Reseller Reference', 1),
(6, 'Inbound enquiry', 1),
(7, 'Advertisements', 1),
(8, 'Exhibitions', 1),
(9, 'Website', 1),
(10, 'Partner', 1);

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_stage`
--

CREATE TABLE IF NOT EXISTS `crms_lead_stage` (
  `lead_stage_id` int(5) NOT NULL AUTO_INCREMENT,
  `lead_stage_name` varchar(64) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`lead_stage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=21 ;

--
-- Dumping data for table `crms_lead_stage`
--

INSERT INTO `crms_lead_stage` (`lead_stage_id`, `lead_stage_name`, `sequence`, `status`) VALUES
(1, 'Initial', 0, 1),
(2, 'Prospect', 1, 1),
(3, 'POC in Progress', 4, 1),
(4, 'Demo Scheduled', 2, 1),
(5, 'Proposal WIP', 6, 1),
(6, 'Proposal Under Review', 7, 1),
(7, 'Proposal Sent to client. Awaiting Approval', 5, 1),
(19, 'Declined', 3, 0),
(9, 'Proposal Accepted. Convert to SOW', 8, 1),
(10, 'SOW under Review', 9, 1),
(11, 'SOW Sent to Client. Awaiting Sign off', 10, 1),
(12, 'SOW Approved. Create Project Charter', 11, 1),
(13, 'Project Charter Approved.', 12, 1),
(20, 'Test stage1', 13, 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_stage_history`
--

CREATE TABLE IF NOT EXISTS `crms_lead_stage_history` (
  `jobid` int(11) NOT NULL,
  `dateofchange` datetime NOT NULL,
  `previous_status` int(11) NOT NULL,
  `changed_status` int(11) NOT NULL,
  `lead_status` int(1) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_stage_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_status_history`
--

CREATE TABLE IF NOT EXISTS `crms_lead_status_history` (
  `jobid` int(11) NOT NULL,
  `dateofchange` datetime NOT NULL,
  `changed_status` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_status_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_levels`
--

CREATE TABLE IF NOT EXISTS `crms_levels` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(32) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `inactive` tinyint(4) NOT NULL,
  PRIMARY KEY (`level_id`),
  UNIQUE KEY `level_id` (`level_id`),
  KEY `level_name` (`level_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `crms_levels`
--

INSERT INTO `crms_levels` (`level_id`, `level_name`, `created_by`, `modified_by`, `created`, `modified`, `inactive`) VALUES
(1, 'Level 1', 59, 59, '2012-12-05 18:31:25', '2013-03-27 16:33:46', 0),
(2, 'Level 2', 59, 116, '2012-12-05 18:33:49', '2013-03-27 15:43:04', 0),
(3, 'Level 3', 59, 116, '2012-12-06 16:04:38', '2013-03-27 15:44:06', 0),
(4, 'Level 4', 59, 116, '2012-12-06 19:42:03', '2013-03-27 15:44:20', 0),
(5, 'Level 5', 59, 59, '2012-12-11 17:23:35', '2013-03-27 16:48:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_levels_country`
--

CREATE TABLE IF NOT EXISTS `crms_levels_country` (
  `levels_country_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `country_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`levels_country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_levels_country`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_levels_location`
--

CREATE TABLE IF NOT EXISTS `crms_levels_location` (
  `levels_location_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `location_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`levels_location_id`),
  KEY `levels_location_id` (`levels_location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_levels_location`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_levels_region`
--

CREATE TABLE IF NOT EXISTS `crms_levels_region` (
  `levels_region_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `region_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`levels_region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_levels_region`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_levels_state`
--

CREATE TABLE IF NOT EXISTS `crms_levels_state` (
  `levels_state_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `state_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`levels_state_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_levels_state`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_location`
--

CREATE TABLE IF NOT EXISTS `crms_location` (
  `locationid` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(30) NOT NULL,
  `stateid` int(10) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` int(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`locationid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_location`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_logs`
--

CREATE TABLE IF NOT EXISTS `crms_logs` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `userid_fk` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_content` text NOT NULL,
  `stickie` int(11) NOT NULL DEFAULT '0',
  `time_spent` int(11) DEFAULT NULL,
  `attached_docs` text,
  PRIMARY KEY (`logid`),
  KEY `jobid_fk` (`jobid_fk`),
  KEY `userid_fk` (`userid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_masters`
--

CREATE TABLE IF NOT EXISTS `crms_masters` (
  `masterid` int(11) NOT NULL AUTO_INCREMENT,
  `master_parent_id` bigint(20) NOT NULL,
  `master_name` varchar(30) NOT NULL,
  `controller_name` varchar(250) NOT NULL,
  `links_to` varchar(250) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` int(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`masterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=128 ;

--
-- Dumping data for table `crms_masters`
--

INSERT INTO `crms_masters` (`masterid`, `master_parent_id`, `master_name`, `controller_name`, `links_to`, `created_by`, `modified_by`, `created`, `modified`, `order_id`, `inactive`) VALUES
(49, 0, 'Active Leads', 'leads', 'leads', 59, 59, '2012-11-19 10:37:56', '2013-07-31 19:45:04', 0, 1),
(50, 47, 'Declined Leads', 'leads', 'leads/declined', 59, 59, '2012-11-19 10:38:31', '2012-11-19 10:38:31', 0, 0),
(51, 0, 'Leads', 'welcome', 'welcome/quotation', 59, 59, '2012-11-19 10:39:24', '2013-09-03 15:26:13', 1, 0),
(52, 51, 'New Lead', 'welcome', 'welcome/new_quote', 59, 59, '2012-11-19 10:39:59', '2012-11-28 17:00:55', 0, 0),
(53, 51, 'Leads List', 'welcome', 'welcome/quotation', 59, 59, '2012-11-19 10:40:20', '2012-12-20 17:20:47', 0, 0),
(62, 110, 'Invoices - Pending Deposit', 'invoice', 'invoice/approved', 59, 59, '2012-11-19 10:47:20', '2013-03-19 18:38:00', 0, 1),
(63, 110, 'Projects - Lists', 'project', 'project', 59, 59, '2012-11-19 10:47:52', '2013-10-23 16:08:06', 0, 0),
(64, 110, 'Invoices - Pending Settlement', 'invoice', 'invoice/settlement', 59, 59, '2012-11-19 10:48:30', '2013-03-19 18:38:23', 0, 1),
(65, 110, 'Projects - Completed', 'invoice', 'invoice/projects/completed', 59, 59, '2012-11-19 10:49:13', '2013-09-05 13:33:47', 0, 1),
(66, 110, 'Projects - Cancelled', 'invoice', 'invoice/projects/p_cancelled', 59, 59, '2012-11-19 10:49:41', '2013-09-05 13:34:11', 0, 1),
(67, 110, 'Invoices - Packages', 'invoice', 'invoice/package', 59, 59, '2012-11-19 10:52:53', '2013-03-19 18:38:39', 0, 1),
(68, 110, 'Subscriptions - generate invoi', 'invoice', 'invoice/billing', 59, 59, '2012-11-19 10:53:42', '2013-03-19 19:18:20', 0, 1),
(69, 0, 'Subscriptions', 'subscription', 'subscription', 59, 59, '2012-11-19 10:54:24', '2012-11-27 10:11:31', 5, 1),
(70, 69, 'Invoices - Pending Payment', 'subscription', 'subscription/s_pending', 59, 59, '2012-11-19 10:55:15', '2012-11-19 10:55:15', 0, 0),
(71, 69, 'Invoices - Settled', 'subscription', 'subscription/s_settled', 59, 59, '2012-11-19 10:56:02', '2012-11-19 10:56:02', 0, 0),
(72, 69, 'Invoices - Cancelled', 'subscription', 'subscription/s_cancelled', 59, 59, '2012-11-19 10:56:55', '2012-11-19 10:56:55', 0, 0),
(73, 69, 'Subscription - Packages', 'subscription', 'subscription/package', 59, 59, '2012-11-19 10:57:20', '2012-11-19 10:57:20', 0, 0),
(74, 0, 'Production', 'production', 'production', 59, 59, '2012-11-19 10:57:52', '2013-02-12 16:58:22', 6, 1),
(75, 74, 'Production', 'production', 'production', 59, 59, '2012-11-19 10:58:43', '2012-11-19 10:58:43', 0, 0),
(76, 74, 'Production- Packages', 'production', 'production/package', 59, 59, '2012-11-19 10:59:07', '2012-11-19 11:55:53', 0, 0),
(80, 109, 'Package Type', 'package', 'package/type', 59, 59, '2012-11-19 11:03:00', '2012-12-20 19:27:16', 0, 0),
(82, 109, 'Package', 'package', 'package/', 59, 59, '2012-11-19 11:06:13', '2012-12-20 19:28:39', 0, 0),
(84, 0, 'Customer', 'customers', 'customers', 59, 59, '2012-11-19 11:07:16', '2013-09-03 15:27:08', 3, 0),
(89, 0, 'My Profile', 'myaccount', 'myaccount', 59, 59, '2012-11-19 11:10:51', '2013-09-03 15:27:20', 5, 0),
(92, 0, 'Administration', 'master', 'user', 59, 59, '2012-11-19 11:12:43', '2013-09-13 19:36:27', 10, 0),
(93, 92, 'Users', 'user', 'user', 59, 59, '2012-11-19 11:13:00', '2013-09-12 18:45:42', 1, 0),
(95, 92, 'Region Settings', 'regionsettings', 'regionsettings/region_settings', 59, 59, '2012-11-19 11:13:56', '2013-08-28 17:40:26', 2, 0),
(96, 92, 'Levels', 'regionsettings', 'regionsettings/level/', 59, 59, '2012-11-19 11:14:23', '2013-04-15 17:33:01', 0, 1),
(97, 92, 'Module', 'master', 'master/', 59, 59, '2012-11-19 11:14:44', '2013-09-13 19:36:55', 3, 1),
(99, 92, 'Roles', 'role', 'role', 59, 59, '2012-11-19 11:15:33', '2013-08-28 17:41:06', 4, 0),
(101, 0, 'Quote Item', 'item_mgmt', 'item_mgmt', 59, 59, '2012-11-19 11:26:10', '2013-09-03 15:28:36', 7, 0),
(102, 101, 'Additional Items', 'item_mgmt', 'item_mgmt', 59, 59, '2012-11-19 11:26:32', '2012-11-21 16:55:04', 0, 0),
(104, 101, 'Item Categories', 'item_mgmt', 'item_mgmt/category_list', 59, 59, '2012-11-19 11:27:12', '2012-11-19 11:27:12', 0, 0),
(106, 109, 'Package Due Date', 'hosting', 'hosting', 59, 59, '2012-11-19 11:51:43', '2012-12-20 19:33:59', 0, 0),
(107, 84, 'Customer List', 'customers', 'customers/', 59, 59, '2012-11-19 12:04:38', '2012-11-21 12:54:22', 0, 0),
(108, 0, 'Tasks', 'tasks', 'tasks/all', 59, 59, '2012-12-20 11:58:15', '2013-09-03 15:27:01', 2, 0),
(109, 0, 'Hosting', 'hosting', 'hosting/', 59, 59, '2012-12-20 19:26:33', '2013-09-03 15:28:23', 6, 0),
(110, 0, 'Projects', 'project', 'project', 59, 59, '2012-12-20 19:29:50', '2013-10-23 16:07:43', 8, 0),
(111, 84, 'Import Customer List', 'import customer list', 'importcustomers', 59, 59, '2013-01-29 10:56:38', '2013-05-16 13:06:14', 2, 1),
(112, 110, 'Projects - On Hold', 'invoice', 'invoice/projects/p_onhold', 59, 59, '2013-03-19 18:42:18', '2013-09-05 13:34:58', 4, 1),
(113, 0, 'Reports', 'report_lead_region', 'report/report_lead_region/', 59, 59, '2013-07-24 11:30:02', '2013-09-03 15:28:56', 9, 0),
(114, 113, 'Leads By Region', 'report', 'report/report_lead_region', 59, 59, '2013-08-06 20:04:15', '2013-08-19 17:04:02', 0, 0),
(115, 113, 'Leads By Owner', 'report_lead_owner', 'report/report_lead_owner', 59, 59, '2013-08-19 17:01:45', '2013-08-19 17:06:07', 0, 0),
(116, 113, 'Leads By Assignee', 'report_lead_assignee', 'report/report_lead_assignee', 59, 59, '2013-08-19 17:06:42', '2013-08-19 17:06:42', 0, 0),
(117, 113, 'Active Leads', 'report_active_lead', 'report/report_active_lead', 59, 59, '2013-08-19 17:07:20', '2013-08-19 17:07:20', 0, 0),
(118, 113, 'Least active leads', 'report_least_active_lead', 'report/report_least_active_lead', 59, 59, '2013-08-19 17:07:59', '2013-08-19 17:07:59', 0, 0),
(119, 92, 'Service Catalogue', 'manage_service', 'manage_service', 59, 59, '2013-08-19 17:08:53', '2013-08-28 17:42:26', 7, 0),
(120, 92, 'Sales Divisions', 'manage_service', 'manage_service/manage_sales', 59, 59, '2013-08-20 16:31:01', '2013-08-28 17:42:04', 5, 0),
(121, 92, 'Lead Sources', 'manage_service', 'manage_service/manage_leadSource', 59, 59, '2013-08-20 16:47:09', '2013-09-12 18:50:25', 6, 0),
(122, 108, 'Task Alert', 'task_alert', 'task_alert', 59, 59, '2013-07-22 18:26:36', '2013-07-22 18:26:36', 1, 0),
(123, 92, 'Lead Stages', 'manage_lead_stage', 'manage_lead_stage', 59, 59, '2013-08-28 17:43:42', '2013-09-12 18:50:37', 8, 0),
(124, 92, 'Currency Type', 'manage_service', 'manage_service/manage_expt_worth_cur', 59, 59, '2013-09-13 18:03:10', '2013-09-13 18:03:10', 9, 0),
(125, 92, 'Client Logo', 'client_logo', 'client_logo', 59, 59, '2013-09-17 15:13:43', '2013-09-17 15:13:43', 10, 0),
(126, 89, 'Manage Notifications', 'notifications', 'notifications', 59, 59, '2013-09-26 15:20:15', '2013-09-26 15:20:51', 1, 0),
(127, 92, 'Email Template', 'email_template', 'email_template', 59, 59, '2013-11-04 16:29:17', '2013-11-04 16:29:17', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_master_roles`
--

CREATE TABLE IF NOT EXISTS `crms_master_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `masterid` bigint(20) NOT NULL,
  `view` tinyint(1) NOT NULL,
  `add` tinyint(1) NOT NULL,
  `edit` tinyint(1) NOT NULL,
  `delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `crms_master_roles`
--

INSERT INTO `crms_master_roles` (`id`, `role_id`, `masterid`, `view`, `add`, `edit`, `delete`) VALUES
(1, 1, 51, 1, 1, 1, 1),
(2, 1, 113, 1, 1, 1, 1),
(3, 1, 110, 1, 1, 1, 1),
(4, 1, 109, 1, 1, 1, 1),
(5, 1, 108, 1, 1, 1, 1),
(6, 1, 101, 1, 1, 1, 1),
(7, 1, 92, 1, 1, 1, 1),
(8, 1, 89, 1, 1, 1, 1),
(9, 1, 84, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `crms_milestones`
--

CREATE TABLE IF NOT EXISTS `crms_milestones` (
  `milestoneid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL,
  `milestone` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`milestoneid`),
  KEY `jobid_fk` (`jobid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_milestones`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_package`
--

CREATE TABLE IF NOT EXISTS `crms_package` (
  `package_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(250) NOT NULL,
  `package_price` decimal(7,2) DEFAULT NULL,
  `typeid_fk` bigint(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `duration` int(5) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`package_id`),
  KEY `typeid_fk` (`typeid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_package`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_package_type`
--

CREATE TABLE IF NOT EXISTS `crms_package_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(250) NOT NULL,
  `type_months` varchar(3) NOT NULL,
  `package_flag` varchar(10) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_package_type`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_region`
--

CREATE TABLE IF NOT EXISTS `crms_region` (
  `regionid` int(11) NOT NULL AUTO_INCREMENT,
  `region_name` varchar(30) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` int(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`regionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_region`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_roles`
--

CREATE TABLE IF NOT EXISTS `crms_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  `inactive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `crms_roles`
--

INSERT INTO `crms_roles` (`id`, `name`, `created`, `modified`, `created_by`, `modified_by`, `inactive`) VALUES
(1, 'Administrator', '0000-00-00 00:00:00', '2013-07-24 11:30:23', 59, 59, 0),
(2, 'Management', '2012-12-11 17:19:28', '2013-07-08 15:09:53', 59, 59, 0),
(3, 'Project Manager', '2012-12-06 12:11:51', '2013-11-06 09:32:26', 59, 59, 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_sales_divisions`
--

CREATE TABLE IF NOT EXISTS `crms_sales_divisions` (
  `div_id` int(11) NOT NULL AUTO_INCREMENT,
  `division_name` varchar(150) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`div_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `crms_sales_divisions`
--

INSERT INTO `crms_sales_divisions` (`div_id`, `division_name`, `status`) VALUES
(1, 'eNoah iSolution India', 1),
(2, 'eNoah iSolution Singapore', 1),
(3, 'eNoah iSolution US', 1),
(4, 'eNoah iSolution Australia', 1);

-- --------------------------------------------------------

--
-- Table structure for table `crms_sessions`
--

CREATE TABLE IF NOT EXISTS `crms_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` longtext,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crms_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_state`
--

CREATE TABLE IF NOT EXISTS `crms_state` (
  `stateid` int(11) NOT NULL AUTO_INCREMENT,
  `state_name` varchar(45) DEFAULT NULL,
  `countryid` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `modified_by` bigint(20) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stateid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_state`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_taskremarks`
--

CREATE TABLE IF NOT EXISTS `crms_taskremarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remarks` varchar(200) NOT NULL,
  `taskid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `createdon` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_taskremarks`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_tasks`
--

CREATE TABLE IF NOT EXISTS `crms_tasks` (
  `taskid` int(11) NOT NULL AUTO_INCREMENT,
  `jobid_fk` int(11) NOT NULL DEFAULT '0',
  `userid_fk` int(11) NOT NULL,
  `task` text,
  `approved` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `is_complete` tinyint(4) NOT NULL DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `actualstart_date` datetime DEFAULT '0000-00-00 00:00:00',
  `actualend_date` datetime DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `hours` int(11) NOT NULL DEFAULT '0',
  `mins` int(11) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `marked_100pct` datetime DEFAULT NULL,
  `marked_complete` datetime DEFAULT NULL,
  `require_qc` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(1) NOT NULL,
  `remarks` varchar(200) NOT NULL,
  PRIMARY KEY (`taskid`),
  KEY `jobid_fk` (`jobid_fk`),
  KEY `userid_fk` (`userid_fk`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_tasks`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_tasks_qc`
--

CREATE TABLE IF NOT EXISTS `crms_tasks_qc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_name` varchar(250) NOT NULL,
  `qc_group` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_tasks_qc`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_tasks_track`
--

CREATE TABLE IF NOT EXISTS `crms_tasks_track` (
  `tasktrackid` int(11) NOT NULL AUTO_INCREMENT,
  `taskid_fk` int(11) NOT NULL,
  `event` varchar(200) NOT NULL,
  `date` datetime NOT NULL,
  `event_data` text,
  PRIMARY KEY (`tasktrackid`),
  KEY `taskid_fk` (`taskid_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_tasks_track`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_task_alert`
--

CREATE TABLE IF NOT EXISTS `crms_task_alert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_alert_days` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `crms_task_alert`
--

INSERT INTO `crms_task_alert` (`id`, `task_alert_days`) VALUES
(1, '2');

-- --------------------------------------------------------

--
-- Table structure for table `crms_users`
--

CREATE TABLE IF NOT EXISTS `crms_users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(150) NOT NULL,
  `add_email` varchar(200) DEFAULT NULL,
  `use_both_emails` tinyint(4) NOT NULL DEFAULT '0',
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `level` tinyint(11) unsigned NOT NULL,
  `is_pm` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Is production manager?',
  `sales_code` varchar(10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `signature` text,
  `key` int(11) NOT NULL DEFAULT '0',
  `bldg_key` int(11) NOT NULL DEFAULT '0',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- Dumping data for table `crms_users`
--

INSERT INTO `crms_users` (`userid`, `role_id`, `first_name`, `last_name`, `password`, `email`, `add_email`, `use_both_emails`, `phone`, `mobile`, `level`, `is_pm`, `sales_code`, `start_date`, `signature`, `key`, `bldg_key`, `inactive`) VALUES
(59, 1, 'Admin', 'eNoah - iSolution', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'admin@enoahisolution.com', '0', 0, '', '9962673215', 1, 1, '0', NULL, 'eNoah - iSolution', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `crms_user_attendance`
--

CREATE TABLE IF NOT EXISTS `crms_user_attendance` (
  `userid_fk` int(11) NOT NULL,
  `login_date` date NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_addr` varchar(100) NOT NULL,
  `logout_time` timestamp NULL DEFAULT NULL,
  `tasks_today` text,
  `tasks_nextday` text,
  PRIMARY KEY (`userid_fk`,`login_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crms_user_attendance`
--


-- --------------------------------------------------------

--
-- Table structure for table `crms_user_roles`
--

CREATE TABLE IF NOT EXISTS `crms_user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_roles_roles` (`role_id`),
  KEY `fk_user_roles_users1` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `crms_user_roles`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
