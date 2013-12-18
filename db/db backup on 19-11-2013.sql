-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 19, 2013 at 08:14 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

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
(19, 'Terms'),
(20, 'Legal');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `crms_client_logo`
--

INSERT INTO `crms_client_logo` (`id`, `filename`, `client_url`) VALUES
(1, '1384764783-web-logo.png', 'www.enoah.in');

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

INSERT INTO `crms_contract_jobs` (`jobid_fk`, `userid_fk`) VALUES
(25, 158),
(36, 147),
(36, 148),
(36, 152),
(37, 158),
(43, 135),
(43, 145),
(44, 147),
(45, 158),
(46, 147),
(46, 148),
(46, 160),
(56, 158),
(56, 163),
(56, 173),
(61, 158),
(63, 158),
(79, 158),
(79, 172),
(79, 173),
(87, 158),
(87, 163),
(87, 172),
(94, 157),
(95, 135),
(95, 154),
(95, 163);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Dumping data for table `crms_country`
--

INSERT INTO `crms_country` (`countryid`, `country_name`, `regionid`, `created_by`, `modified_by`, `created`, `modified`, `inactive`) VALUES
(21, 'Ireland', 2, 59, 59, '2013-01-22 18:15:51', '2013-11-04 20:47:36', 1),
(20, 'France', 2, 59, 59, '2013-01-22 18:15:46', '2013-01-22 18:15:46', 0),
(19, 'United Kingdom', 2, 59, 59, '2013-01-22 18:15:36', '2013-01-22 18:15:36', 0),
(18, 'Australia', 1, 59, 59, '2013-01-22 18:07:55', '2013-01-22 18:07:55', 0),
(17, 'Singapore', 1, 59, 59, '2013-01-22 18:07:44', '2013-01-22 18:07:44', 0),
(16, 'Malaysia', 1, 59, 59, '2013-01-22 18:07:39', '2013-01-22 18:07:39', 0),
(15, 'India', 1, 59, 59, '2013-01-22 17:50:19', '2013-11-19 16:48:44', 0),
(22, 'Germany', 2, 59, 59, '2013-01-22 18:15:58', '2013-01-22 18:15:58', 0),
(23, 'USA', 3, 59, 59, '2013-01-22 18:16:27', '2013-01-22 18:16:27', 0),
(24, 'Saint Lucia', 4, 59, 59, '2013-01-29 15:24:56', '2013-01-29 15:24:56', 1),
(30, 'Japan', 8, 59, 59, '2013-02-01 09:43:11', '2013-02-01 09:43:11', 0),
(27, 'Test Country', 6, 145, 145, '2013-01-29 17:35:10', '2013-01-29 17:36:37', 0),
(28, 'test 2', 6, 145, 145, '2013-01-29 18:32:25', '2013-01-29 18:32:25', 0),
(29, 'south africa', 7, 59, 59, '2013-01-31 17:40:06', '2013-01-31 17:40:06', 0),
(31, 'south africa2', 9, 59, 59, '2013-02-04 18:04:04', '2013-02-04 18:04:04', 0),
(32, 'Antartica Country', 11, 59, 59, '2013-02-05 11:33:33', '2013-11-05 13:53:45', 1),
(33, '', 12, 59, 59, '2013-02-05 12:05:50', '2013-02-05 12:05:50', 0),
(34, 'Test6', 13, 59, 59, '2013-02-05 12:33:12', '2013-02-05 12:33:12', 0),
(35, 'test cont', 14, 59, 59, '2013-02-05 15:43:42', '2013-02-05 15:43:42', 1),
(36, '213131321', 16, 59, 59, '2013-02-07 09:42:57', '2013-02-07 09:42:57', 0),
(37, 'test contry', 14, 59, 59, '2013-02-14 16:23:19', '2013-02-14 16:23:19', 1),
(38, 'test country1', 14, 59, 59, '2013-02-14 16:30:37', '2013-02-14 16:30:37', 1),
(39, 'Paksitan', 1, 59, 59, '2013-02-14 16:32:58', '2013-02-14 16:32:58', 0),
(40, 'test c', 14, 59, 59, '2013-02-14 17:40:08', '2013-11-19 16:27:10', 1),
(44, 'South Country', 28, 150, 150, '2013-02-15 20:46:47', '2013-02-15 20:46:47', 0),
(42, 'India', 27, 59, 59, '2013-02-15 17:08:28', '2013-02-15 17:08:28', 0),
(43, 'Country 65', 27, 59, 59, '2013-02-15 17:12:51', '2013-02-15 17:12:51', 0),
(45, 'Africa2', 43, 59, 59, '2013-02-20 11:45:41', '2013-11-19 16:40:43', 1),
(46, 'Japan', 1, 59, 59, '2013-02-20 11:50:12', '2013-02-20 11:50:12', 0),
(47, 'Africa cnty', 44, 59, 59, '2013-02-20 15:57:07', '2013-11-19 10:19:04', 0),
(48, 'South America country', 45, 59, 59, '2013-03-13 16:36:13', '2013-03-13 16:36:13', 0),
(49, 'paci1', 1, 59, 59, '2013-03-15 10:47:50', '2013-03-15 10:47:50', 0),
(50, 'Brazil', 46, 59, 59, '2013-03-27 15:20:33', '2013-03-27 15:20:33', 0),
(53, 'Zimbabwe', 46, 139, 59, '2013-05-06 16:51:07', '2013-11-05 10:15:52', 1),
(55, 'ccc1', 47, 59, 59, '2013-11-19 16:47:28', '2013-11-19 17:46:26', 0),
(56, 'ccc2', 47, 59, 59, '2013-11-19 17:46:39', '2013-11-19 17:46:39', 0),
(57, 'ccc3', 47, 59, 59, '2013-11-19 17:46:48', '2013-11-19 17:46:48', 0);

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

INSERT INTO `crms_crons_notificatons` (`userid`, `cron_id`, `onscreen_notify_status`, `email_notify_status`, `no_of_days`) VALUES
(158, 1, 1, 1, 7),
(158, 2, 1, 1, 7),
(59, 1, 1, 1, 1),
(161, 1, 1, 0, 1),
(160, 1, 1, 0, 1),
(152, 2, 1, 1, 1),
(174, 1, 1, 1, 30),
(59, 2, 0, 1, 1),
(173, 2, 0, 1, 7),
(173, 1, 1, 0, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `crms_currency_rate`
--

INSERT INTO `crms_currency_rate` (`id`, `from`, `to`, `value`) VALUES
(1, 1, 5, '62.45'),
(2, 2, 5, '58.784'),
(3, 3, 5, '50.12'),
(4, 4, 5, '19.605'),
(5, 6, 5, '84.333'),
(6, 5, 5, '1');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

--
-- Dumping data for table `crms_customers`
--

INSERT INTO `crms_customers` (`custid`, `first_name`, `last_name`, `position_title`, `company`, `abn`, `add1_line1`, `add1_line2`, `add1_suburb`, `add1_region`, `add1_country`, `add1_state`, `add1_location`, `add1_postcode`, `phone_1`, `phone_2`, `phone_3`, `phone_4`, `email_1`, `email_2`, `email_3`, `email_4`, `www_1`, `www_2`, `comments`, `exported`, `skype_name`) VALUES
(12, 'sdsad', 'sdsad', '', 'dsadsa', NULL, '', '', '', '1', '15', '24', '9', '', '54545', '', '', '', 'msurya@gmail.com', '', '', '', '', '', '', NULL, ''),
(14, 'CRM', 'eNoah', '', 'eNoah', NULL, '', '', '', '1', '18', '50', '65', '', '4564654', '', '', '', 'ssriram@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(18, 'sample', 'customer', '', 'enoah', NULL, '', '', '', '1', '15', '24', '45', '', '324224', '', '', '', 'ssriram@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(22, 'VVM', 'Pigments', '', 'eNoa', NULL, '', '', '', '8', '30', '125', '26', '', '5654654', '', '', '', 'wsds@gmail.com', '', '', '', '', '', '', NULL, ''),
(23, 'DACSS', 'S', '', 'eNoa', NULL, '', '', '', '8', '30', '125', '26', '', '4543636', '', '', '', 'sd@gmail.com', '', '', '', '', '', '', NULL, ''),
(32, 'Nagendra', 'P', '', 'eNoa', NULL, '', '', '', '8', '30', '125', '25', '', '1245454545', '', '', '', 'ttyt@gmail.com', '', '', '', '', '', '', NULL, ''),
(33, 'test', 'tecu', '', 'test', NULL, '', '', '', '1', '15', '1', '7', '', '23424234', '', '', '', 'tewst@test.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(44, 'Dinesh', 'Anand', 'Position1', 'eNoah', NULL, 'No 37', 'West street', 'Tanjore', '27', '42', '144', '42', '600041', '44236598', '449874562', '9790074370', '147984654', 'dinesh@gmail.com', 'dinesh@ymail.com', 'dinesh@yahoo.in', 'dinesh@yahoo.com', 'www.dinesh.com', 'www.dinesh.in', 'any other comments', NULL, 'dinesh65'),
(45, 'Test', '65', '65th position', 'Company 65', NULL, 'address 65', 'address2 65', 'sub 65', '27', '43', '145', '43', '613005', '12345789065', '12345789065', '9874065650', '657891230', 'dinesh65@gmail.com', 'dinesh65@gmail.com', 'dinesh65@gmail.com', 'dinesh65@gmail.com', 'dinesh.in', 'dinesh.com', 'any other comments', NULL, 'dinesh65'),
(46, 'Muthurajan', 'R', '', 'VV Minerals', NULL, '', '', '', '1', '15', '24', '45', '', '9442182337', '', '', '', 'muthurajan@vvmineral.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(48, 'Lokesh', 'Babu', '', 'OriginWave', NULL, '', '', '', '1', '15', '24', '3', '', '9791069752', '', '', '', 'lokesh@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(49, 'sir', 'ji', '', 'siri', NULL, '', '', '', '1', '15', '24', '9', '', '', '', '', '', 'siriji@test.com', '', '', '', '', '', '', NULL, ''),
(50, 'santha1', 'k1', 'CA', 'enoasa1', NULL, 'burkit road', 'T.nagar', 'test', '1', '39', '140', '38', '600017', '12312312', '42423423', '42423423', '4234', 'ssriram@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(51, 'suraj', 'sai', '', 'sai enterprice', NULL, '', '', '', '1', '15', '24', '3', '', '', '', '', '', 'suraj@ji.com', '', '', '', '', '', '', NULL, ''),
(52, 'Vijay Kumar', 'CH', 'Associate', 'eNoah', NULL, '', '', '', '1', '15', '24', '3', '', '', '', '', '', 'ssriram@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(56, 'Raguram', 'P K', '', 'VV Ti Pigments', NULL, '', '', '', '1', '15', '24', '56', '', '', '', '', '', 'raguram@vvmineral.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(62, 'Raja', 'Durai', 'position72', 'company72', NULL, 'Address 72', 'line 72', 'sub 72', '1', '15', '24', '9', '641072', '1234567890', '1234567890', '8971627246', '12345678', 'raja@gmail.com', 'stranger@gmail.com', NULL, NULL, '', '', NULL, NULL, NULL),
(63, 'Gokul', 'Kan', 'position13', 'company13', NULL, 'Address 13', 'line 13', 'sub 13', '1', '15', '15', '5', '641013', '12345678', '12345678', '9894305956', '12345678', 'stunner@gmail.com', 'gokul@gmail.com', NULL, NULL, '', '', NULL, NULL, NULL),
(66, 'Surya Firm', 'Lastname', '', 'eNoah', NULL, '', '', '', '46', '50', '152', '60', '', '', '', '', '', 'msurya@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(67, 'Vijay', 'Venkat', '', 'eNOah', NULL, '', '', '', '1', '15', '24', '3', '', '', '', '', '', 'ssriram@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(75, 'sample', 'customer', '', 'sampcomp', NULL, '', '', '', '8', '30', '125', '26', '', '', '', '', '', 'kiks@gmail.com', '', '', '', '', '', '', NULL, ''),
(77, 'test', 'test', '', 'Test', NULL, '', '', '', '1', '15', '24', '3', '', '', '', '', '', 'wingsvijay@gmail.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(82, 'Test', 'Test', '', 'Test', NULL, '', '', '', '1', '15', '1', '7', '', '', '', '', '', 'Test@test.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(85, 'tristan', 'v', '', 'mums', NULL, '', '', '', '1', '18', '50', '65', '', '', '', '', '', 'ssriram@enoahisolution.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(88, 'priya', 'v', 'test', 'test company', NULL, 'Adr 1', 'Adr 2', 'sub', '46', '50', '152', '60', '600023', '', '', '', '', 'priya@enoahisolution.com', '', '', '', '', '', 'test comments', NULL, ''),
(92, 'ram', 'krishna', 'l1', 'Ram & Krishna Co', NULL, 'chennai', 'tamil nadu', '', '1', '15', '24', '8', '620006', '9874563210', '', '', '', 'ram@krishna.com', '', '', '', '', '', 'no comments', NULL, 'ram@krishna'),
(94, 'Zimba client', 'Zi', '', 'Zim & co', NULL, '', '', '', '46', '53', '156', '70', '', '', '', '', '', 'ssriram@enoahisolution.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(95, 'Zimba Midlant client', 'Zm', '', 'Zimba Midland & Co', NULL, '', '', '', '46', '53', '157', '71', '', '', '', '', '', 'zimbamid@zim.zi', '', NULL, NULL, '', '', NULL, NULL, NULL),
(96, 'vignesh', 'ger', '', 'ger-vig & Co', NULL, '', '', '', '2', '22', '158', '72', '', '', '', '', '', 'ger-vig@german.gy', '', NULL, NULL, '', '', NULL, NULL, NULL),
(97, 'usha', 'a', '', 'usha fan coporation', NULL, '', '', '', '2', '21', '159', '73', '', '', '', '', '', 'ushafan@irish.irs', '', NULL, NULL, '', '', NULL, NULL, NULL),
(98, 'sri', 'ram', '', 'ram & co', NULL, '', '', '', '1', '15', '1', '7', '', '', '', '', '', 'ramsri_14@yahoo.co.in', '', '', '', '', '', '', NULL, ''),
(100, 'Prakash', 'Raj', '', 'Company', NULL, '', 'asdf', '', '1', '15', '1', '7', 'asddf', '', '', '', '', 'ramsri@yahoo.co.in', '', '', '', '', '', '', NULL, ''),
(101, 'sri', 'ram', 'l1', 'RM & co ltd', NULL, 'chennai', 'tamil nadu', '', '1', '15', '24', '45', '620006', '9874563210', '', '', '', 'ramsri_14@gmail.com', '', '', '', '', '', 'no comments', NULL, 'raamsri14'),
(102, 'satis', 'r', '', 'Readings', NULL, '', '', '', '1', '18', '47', '74', '', '', '', '', '', 'rsathishkumar@enoahisolution.com', '', '', '', '', '', '', NULL, ''),
(103, 'shankar', 'r', 'tester', 'jukebox', NULL, '', '', '', '1', '18', '47', '75', '', '', '', '', '', 'rshanksar@gmail.com', '', '', '', '', '', '', NULL, ''),
(104, 'te4st', 'asdf', '', 'asdf', NULL, '', '', '', '11', '32', '137', '35', '', '', '', '', '', 'test@atest.co', '', '', '', '', '', 'test', NULL, ''),
(105, 'giri', 'v', '', 'giri corp services', NULL, '', '', '', '1', '17', '113', '1', '', '', '', '', '', 'giri@gr.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(106, 'prat', 'v', '', 'prat comp', NULL, '', '', '', '45', '48', '151', '58', '', '', '', '', '', 'prat@en.co', '', NULL, NULL, '', '', NULL, NULL, NULL),
(107, 'Swaminathan', 'D', '', 'Swami Group Companies', NULL, '', '', '', '1', '15', '24', '62', '', '', '', '', '', 'raamsri14@gmail.com', '', '', '', '', '', '', NULL, ''),
(108, 'Swami', 'S', '', 'SSM Group', NULL, '', '', '', '1', '18', '50', '65', '', '', '', '', '', 'sswami@enoahisolution.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(109, 'Anbarasan', 'K', '', 'KA Company', NULL, '', '', '', '1', '18', '47', '75', '', '', '', '', '', 'kanbu@enoahisolution.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(110, 'Peter', 'Kein', '', 'APS', NULL, '', '', '', '3', '23', '65', '76', '', '', '', '', '', 'rira14@rediff.com', '', NULL, NULL, '', '', NULL, NULL, NULL),
(111, 'test customer', 'taaasdf ', '', 'aa', NULL, '', '', '', '1', '15', '24', '62', '', '', '', '', '', 'sriram@gmail.com', '', '', '', '', '', 'asasdf asdf ', NULL, '');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `crms_deposits`
--

INSERT INTO `crms_deposits` (`depositid`, `jobid_fk`, `invoice_no`, `amount`, `deposit_date`, `comments`, `userid_fk`, `date`, `payment_received`, `map_term`) VALUES
(5, 45, 'Inv009', '1000.00', '2013-04-17 00:00:00', 'Payment received', NULL, NULL, 1, '6'),
(6, 45, 'Inv010', '500.00', '2013-04-19 00:00:00', 'Partial payment', NULL, NULL, 1, '7'),
(7, 33, '7yj788', '500.00', '2013-04-25 00:00:00', 'asdfasddf', 59, NULL, 1, '9'),
(8, 46, '7yj788qe3r', '1000.00', '2013-04-25 00:00:00', 'asdfasdf', 59, NULL, 1, '4'),
(44, 37, 'Inv 001', '1000.00', '2013-04-02 00:00:00', '', 59, NULL, 1, '17'),
(45, 37, 'Inv 002', '1000.00', '2013-04-10 00:00:00', '', 59, NULL, 1, '18'),
(46, 37, 'Inv 003', '500.00', '2013-04-24 00:00:00', '', 59, NULL, 1, '18'),
(47, 55, 'inv-001', '250.00', '2013-05-10 00:00:00', '', 59, NULL, 1, '20'),
(48, 55, 'inv234', '250.00', '2013-05-10 00:00:00', '', NULL, NULL, 1, '20'),
(49, 61, 'p1', '1000.00', '2013-07-14 00:00:00', 'test', NULL, NULL, 1, '21'),
(50, 63, 'bill no 12', '600.00', '2013-07-10 00:00:00', 'test', 59, NULL, 1, '22'),
(51, 63, 'bill no 15', '200.00', '2013-07-10 00:00:00', 'test', NULL, NULL, 1, '22'),
(52, 90, '3432', '600.00', '2013-08-22 00:00:00', 'adsfasdf', NULL, NULL, 1, '23'),
(53, 90, '234ff', '600.00', '2013-08-29 00:00:00', 'asdf asd', NULL, NULL, 1, '23'),
(54, 84, 'INV009', '3000.00', '2013-10-11 00:00:00', '', NULL, NULL, 1, '24'),
(55, 84, 'INV010', '2000.00', '2013-10-11 00:00:00', 'test', 59, NULL, 1, '24'),
(56, 84, 'INV011', '2500.00', '2013-10-11 00:00:00', '', 59, NULL, 1, '25'),
(64, 95, 'atest', '4500.00', '2013-10-10 00:00:00', ' saddfa', 59, NULL, 1, '26'),
(65, 95, 'INV012', '1300.00', '2013-10-15 00:00:00', '200 concession', 59, NULL, 1, '32'),
(66, 95, 'INV0025', '1500.00', '2013-10-28 00:00:00', 'tewrtwer wer', 59, NULL, 1, '26'),
(67, 95, 'inh54', '1222.00', '2013-10-31 00:00:00', 'df asdfa sdf', 59, NULL, 1, '34'),
(68, 79, 'inh54s', '1200.00', '2013-11-03 00:00:00', 'terqewr', 59, NULL, 1, '35');

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

INSERT INTO `crms_dns` (`hostingid`, `host_location`, `login_url`, `login`, `registrar_password`, `tech_contact`, `tech_email`, `tech_name`, `client_contact`, `client_email`, `client_name`, `email`, `go_live_date`, `email_change`, `cur_smtp_setting`, `cur_pop_setting`, `cur_webmail_url`, `cur_controlpanel_url`, `cur_statspanel_url`, `cur_dns_primary_url`, `cur_dns_primary_ip`, `cur_dns_secondary_url`, `cur_dns_secondary_ip`, `cur_record_setting`, `cur_mx_record`, `fut_smtp_setting`, `fut_pop_setting`, `fut_webmail_url`, `fut_controlpanel_url`, `fut_statspanel_url`, `fut_dns_primary_url`, `fut_dns_primary_ip`, `fut_dns_secondary_url`, `fut_dns_secondary_ip`, `fut_record_setting`, `fut_mx_record`, `date_handover`, `host_status`) VALUES
(3, 'enoah domain', 'dfasdf', 'adsf', 'asdf', '213123123', 'asdf@ca.co', 'asdf', '213123123', 'asdf@ca.co', 'Faraq', 'asdf@ca.co', '2013-05-09', '0', '192.168.0.156', '192.168.0.144', 'mail.v2square.com', 'sadf', 'asddf', 'asddf', '192.168.0.132', '192.168.0.12', '192.168.0.134', 'asdf@ca.co', 'asdf@ca.co', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0000-00-00', 0),
(14, '1', 'www.lobin.com', 'yes', 'yes', '', '', '', '', '', '', '', '2013-06-11', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2013-06-05', 2),
(9, '2', 'http://192.168.1.73/ecrm', 'admin', 'admin123', '0431-2422222', 'rira14@rediff.com', 'rira14', '0431-2422222', 'rira14@rediff.com', 'Sriram', 'rira14@rediff.com', '0000-00-00', '0', '192.168.0.206', '192.168.0.201', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'enoahisolution.com', '10.0.2.68', 'enoah.in', '10.0.2.79', 'Nothing', 'Anything', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 2),
(17, 'enoah domain', 'test url', 'test', 'test123', '', '', '', '', '', '', '', '0000-00-00', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 3),
(13, 'Godday Domain godad', 'test url', 'test', 'test123', '9876543210', 'test@test.com', 'any name', '9876543210', 'test@test.com', 'any name', 'test@test.com', '0000-00-00', '0', '192.168.0.206', '192.168.0.201', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'enoahisolution.com', '10.0.2.68', 'enoah.in', '10.0.2.79', 'asdf@ca.co', 'Anything', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 0),
(15, 'Enoahi solution domain', 'test url', 'admin', 'admin123', '0431-2422222', 'rira14@rediff.com', 'rira14', '0431-2422222', 'rira14@rediff.com', 'any name', 'asdf@ca.co', '0000-00-00', '0', '192.168.0.206', '192.168.0.201', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'mail.enoahisolution.com', 'enoahisolution.com', '10.0.2.68', 'enoah.in', '10.0.2.79', 'Nothing', 'Anything', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 0),
(22, 'test domain', 'test', '', '', '', '', '', '', '', '', '', '0000-00-00', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 0),
(18, 'Client Hosting', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 3);

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
(1, 'Lead Notificatiion Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n    <td>\r\n	<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n    <p style="background: none repeat scroll 0 0 #4B6FB9;\r\n    border-bottom: 1px solid #CCCCCC;\r\n    color: #FFFFFF;\r\n    margin: 0;\r\n    padding: 4px;">\r\n        <span>{{print_fancydate}}</span>Â Â Â {{first_name}}Â {{last_name}}</p>\r\n    <p style="padding: 4px;">{{log_content}}<br /><br />\r\n		This log has been emailed to:<br />\r\n		{{received_by}}<br /><br />\r\n		{{signature}}<br />\r\n    </p>\r\n</div>\r\n</td>\r\n  </tr>\r\n\r\n   <tr>\r\n    <td>Â </td>\r\n  </tr>', '2013-11-06 09:52:03'),
(2, 'New Lead Creation Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Lead Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr><td><table style="border:1px #CCC solid; font-family:Arial, Helvetica, sans-serif; font-size:12px;" width="96%" align="center" cellspacing="0" cellpadding="4"><tr><p><td style="border-right:1px #CCC solid; color:#FFF"" width="73" bgcolor="#4B6FB9"><b>Title</b></td><td  style="border-right:1px #CCC solid; color:#FFF""width="41" bgcolor="#4B6FB9"><b>Description</b></td>\r\n</p></tr><tr><td style="border-right:1px #CCC solid;">Client</td><td style="border-right:1px #CCC solid;">{{first_name}} {{last_name}}-{{company}}</td></tr>\r\n<tr style="border:1px #CCC solid;"><td style="border-right:1px #CCC solid;">URL</td><td style="border-right:1px #CCC solid;"><a href="{{base_url}}welcome/view_quote/{{insert_id}}">Click here to View Lead</a></td></tr></table></td></tr>', '2013-11-06 09:52:11'),
(3, 'Lead Re-assignment Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Re-assignment Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n	<td>\r\n		<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n		<p style="background: none repeat scroll 0 0 #4B6FB9;\r\n		border-bottom: 1px solid #CCCCCC;\r\n		color: #FFFFFF;\r\n		margin: 0;\r\n		padding: 4px;">\r\n			<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n			<p style="padding: 4px;">{{log_content}}<br /><br />\r\n				{{signature}}<br />\r\n			</p>\r\n		</div>\r\n	</td>\r\n</tr>', '2013-11-06 09:52:18'),
(4, 'Lead Owner Re-assignment Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Owner Re-assignment Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:25'),
(5, 'Lead - Status Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Status Change Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content_email}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:32'),
(6, 'Lead - Delete Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead Deleted Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:39'),
(7, 'Lead to Project Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Lead to Project Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">{{log_content_email}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 09:52:46'),
(8, 'New Customer Creation', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Customer Creation Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">New Customer Created -{{first_name}}  {{last_name}} - {{company}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 10:47:36'),
(9, 'Customer Details Modification Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Customer Details Modification Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>Â Â Â {{user_name}}</p>\r\n<p style="padding: 4px;">Customer Details Modified - {{first_name}}  {{last_name}} - {{company}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 10:49:57'),
(10, 'User Profile Changes Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Profile Changes Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>Â Â Â {{user_name}}</p>\r\n<p style="padding: 4px;">User Profile Modified - {{first_name}}  {{last_name}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 11:23:21'),
(11, 'New User Creation Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New User Creation Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;">New user Created - {{first_name}}  {{last_name}} <br /><br />\r\nUser Login Details<br /><br />\r\nLogin URL : {{base_url}} <br />User Login email id : {{email}}<br />\r\nPassword : {{password}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 11:51:46'),
(12, 'User Level Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Level Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;"> User Level has been Changed for - {{first_name}}  {{last_name}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 12:01:57'),
(13, 'User Role Change Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">User Role Change Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{user_name}}</p>\r\n<p style="padding: 4px;"> User Role has been Changed for - {{first_name}}  {{last_name}} <br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 12:06:16'),
(14, 'Project Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>&nbsp;&nbsp;&nbsp;{{first_name}}  {{last_name}}</p>\r\n<p style="padding: 4px;"> {{log_content}} <br /><br />\r\nThis log has been emailed to:<br />\r\n{{received_by}}<br /><br />\r\n{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 13:46:16'),
(15, 'Project Assignment / Removal Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>\r\n</p>\r\n<p style="padding: 4px;"> \r\n<span>Hi </span>&nbsp;{{first_name}},</p>\r\n<span>{{email_content}}</span><br /><br />\r\n<span>Regards,</span><br />\r\n<span>Webmaster</span>\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 14:46:12'),
(16, 'Assign / Remove Project Members', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Project Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>\r\n</p>\r\n<p style="padding: 4px;">\r\n<span>Hi </span>Â {{first_name}},<br />\r\n<br />\r\n<span> {{log_email_content}}</span><br /><br />\r\n<span> Regards,</span><br />\r\n<span> Webmaster</span>\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 15:04:52'),
(17, 'Task Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" style="border:1px solid #666666;border-collapse: collapse;">\r\n<tbody><tr>\r\n<td width="80" valign="top" style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask Desc\r\n</td>\r\n<td colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{task_name}}\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;" >\r\nRemarks\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px; ">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;\r\nborder-collapse: collapse;\r\ncolor: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border:1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1;">\r\nStatus\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n{{task_status}} %\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 18:37:29'),
(18, 'Task Delete Notification Message', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Delete Notification Message</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<div  style="border: 1px solid #CCCCCC;margin: 0 0 10px;">\r\n<p style="background: none repeat scroll 0 0 #4B6FB9;\r\nborder-bottom: 1px solid #CCCCCC;\r\ncolor: #FFFFFF;\r\nmargin: 0;\r\npadding: 4px;">\r\n<span>{{print_fancydate}}</span>Â Â Â </p>\r\n<p style="padding: 4px;">{{task_name}}Â  hasÂ beenÂ declinedÂ byÂ {{user_name}}<br /><br />{{signature}}<br />\r\n</p>\r\n</div>\r\n</td>\r\n</tr>', '2013-11-06 16:39:45'),
(19, 'Task Completion Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">Task Completion Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;\r\nborder-collapse: collapse;">\r\n<tbody>\r\n<tr>\r\n<td width="80" valign="top" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask\r\n</td>\r\n<td class="task" colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{task_name}}\r\n</td>\r\n</tr>\r\n<tr>\r\n<td  style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nRemarks\r\n</td>\r\n<td rel="2:0" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated&nbsp;by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nStatus\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n{{task_status}}\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 17:00:54'),
(20, 'New Task Notification', '<tr><td style="padding:13px 1px 1px 1px;"><h3 style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:15px;">New Task Notification</h3></td></tr>', 'webmaster@enoahisolultion.com', '<tr>\r\n<td>\r\n<table cellspacing="0" cellpadding="0" border="0" id="task-table-28" style="border:1px solid #666666;\r\nborder-collapse: collapse;">\r\n<tbody>\r\n<tr>\r\n<td width="80" valign="top" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nTask Desc\r\n</td>\r\n<td class="task" colspan="3" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{job_task}}</a>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocated to\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{taskSetTo}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;" >\r\nRemarks\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">\r\n{{remarks}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned Start Date\r\n</td>\r\n<td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{start_date}}\r\n</td>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nPlanned End Date\r\n</td>\r\n<td style="border:1px solid #666666;border-collapse: collapse;color: #333333; padding: 5px;">\r\n{{end_date}}\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n<td style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nAllocatedÂ by:\r\n</td>\r\n<td width="100" style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px;">{{first_name}} {{last_name}}</td>\r\n<td width="80" style="border: 1px solid #666666; border-collapse: collapse; background: none repeat scroll 0 0 #E7EAF1; padding: 5px;">\r\nStatus\r\n</td>\r\n<td <td style="border:1px solid #666666; border-collapse: collapse; color: #333333; padding: 5px; padding: 5px;">\r\n  {{status}} %\r\n</td>\r\n</tr>\r\n</tbody></table>\r\n</td>\r\n</tr>', '2013-11-06 18:15:42'),
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
(1, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml">\r\n<head>\r\n<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />\r\n<title>Email Template</title>\r\n<style type="text/css">\r\nbody {\r\n	margin: 0px;\r\n}\r\n</style>\r\n</head>\r\n\r\n<body>\r\n<table width="630" align="center" border="0" cellspacing="15" cellpadding="10" bgcolor="#f5f5f5">\r\n<tr><td bgcolor="#FFFFFF">\r\n<table width="600" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">\r\n  <tr>\r\n    <td style="padding:15px; border-bottom:2px #5a595e solid;">\r\n		<img src="http://crm.enoahisolution.com/assets/img/esmart_logo.jpg" />\r\n	</td>\r\n  </tr>', '<tr>\r\n<td>Â </td>\r\n</tr>\r\n<tr>\r\n    <td style="font-family:Arial, Helvetica, sans-serif; color:#F60; font-size:12px; text-align:center; padding-top:8px; border-top:1px #CCC solid;"><b>Note : Please do not reply to this mail.  This is an automated system generated email.</b></td>\r\n  </tr>\r\n</table>\r\n</td>\r\n</tr>\r\n</table>\r\n</body>\r\n</html>');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `crms_expected_payments`
--

INSERT INTO `crms_expected_payments` (`expectid`, `jobid_fk`, `percentage`, `amount`, `expected_date`, `received`, `comments`, `project_milestone_name`) VALUES
(1, 43, 0, '100.00', '2013-03-19 00:00:00', 1, NULL, 'payment 1'),
(3, 46, 0, '26000.00', '2013-03-27 00:00:00', 1, NULL, 'milestone payment'),
(4, 46, 0, '2000.00', '2013-03-27 00:00:00', 2, NULL, 'Payment1'),
(5, 44, 0, '200.00', '2013-03-29 00:00:00', 1, NULL, 'HH'),
(6, 45, 0, '1000.00', '2013-04-16 00:00:00', 1, NULL, 'test payment1'),
(7, 45, 0, '1000.00', '2013-04-18 00:00:00', 2, NULL, 'test payment2'),
(8, 45, 0, '500.00', '2013-04-20 00:00:00', 0, NULL, 'test payment3'),
(9, 33, 0, '1000.00', '2013-04-19 00:00:00', 2, NULL, 'Payment #1'),
(10, 45, 0, '99999.99', '2013-04-23 00:00:00', 0, NULL, 'Payment #1'),
(17, 37, 0, '1500.00', '2013-02-01 00:00:00', 2, NULL, 'Payment No 1'),
(18, 37, 0, '2000.00', '2013-03-31 00:00:00', 2, NULL, 'Payment No 2'),
(19, 37, 0, '2000.00', '2013-04-10 00:00:00', 0, NULL, 'Payment No 3'),
(20, 55, 0, '1000.00', '2013-05-10 00:00:00', 2, NULL, 'payment 1'),
(21, 61, 0, '1500.00', '2013-07-01 00:00:00', 2, NULL, 'p1'),
(22, 63, 0, '1000.00', '2013-07-09 00:00:00', 2, NULL, 'payment 1'),
(23, 90, 0, '1200.00', '2013-08-08 00:00:00', 1, NULL, 'test payment1'),
(24, 84, 0, '5000.00', '2013-10-10 00:00:00', 1, NULL, 'Milestone 1'),
(25, 84, 0, '5000.00', '2013-10-11 00:00:00', 2, NULL, 'Milestone 2'),
(26, 95, 0, '6000.00', '2013-10-01 00:00:00', 1, NULL, 'Milestone 01'),
(32, 95, 0, '1500.00', '2013-10-22 00:00:00', 2, NULL, 'Milestone 02'),
(33, 95, 0, '900.00', '2013-10-01 00:00:00', 0, NULL, 'Milestone 03'),
(34, 95, 0, '1222.00', '2013-10-25 00:00:00', 1, NULL, 'tew'),
(35, 79, 0, '1200.00', '2013-11-12 00:00:00', 1, NULL, 'tewt milestone 01');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `crms_hosting`
--

INSERT INTO `crms_hosting` (`hostingid`, `custid_fk`, `domain_name`, `domain_status`, `expiry_date`, `ssl`, `domain_expiry`, `other_info`) VALUES
(3, 67, 'www.v2square.com', 1, '2013-06-13', 0, '2013-05-09', 'tesrt'),
(12, 52, 'www.vijay.com', 3, '2013-06-06', 0, '2013-06-04', 'asdf'),
(23, 88, 'www.vprijna.com', 1, '2014-05-09', 1, '2013-05-24', 'test'),
(13, 50, 'www.santha.cpl', 3, '2013-05-13', 0, '2013-05-13', 'adsf'),
(15, 18, 'www.samplecus.com', 0, '2014-05-08', 0, '2013-05-06', 'test infomation'),
(17, 14, 'www.evhlhln.on', 1, '2013-06-08', 0, '2014-06-17', 'asdf'),
(18, 85, 'www.mums.sg', 1, '2014-05-06', 2, '2013-05-02', 'test info'),
(19, 94, 'www.zimbaco.com', 1, '2014-05-06', 0, '2013-05-01', 'asdf'),
(24, 45, 'www.test65.ock', 2, '2014-05-09', 1, '2013-05-29', 'asdf'),
(22, 88, 'www.priyatest.com', 1, '2014-05-06', 0, '2013-05-01', 'test');

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

INSERT INTO `crms_hosting_package` (`hostingid_fk`, `packageid_fk`, `due_date`) VALUES
(18, 2, '0000-00-00'),
(13, 2, '0000-00-00'),
(3, 1, '0000-00-00'),
(19, 2, '0000-00-00'),
(23, 1, '0000-00-00'),
(12, 1, '0000-00-00'),
(23, 2, '0000-00-00'),
(17, 2, '0000-00-00'),
(22, 1, '0000-00-00'),
(24, 2, '0000-00-00'),
(24, 1, '0000-00-00');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=277 ;

--
-- Dumping data for table `crms_items`
--

INSERT INTO `crms_items` (`itemid`, `jobid_fk`, `item_position`, `item_desc`, `item_price`, `hours`, `ledger_code`) VALUES
(2, 2, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(3, 3, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(4, 4, 2, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(9, 8, 7, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(10, 8, 8, '\nFLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '700.00', '0.00', '41000'),
(8, 8, 6, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(11, 8, 9, '\nXHTML / CSS CODING \nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):', '0.00', '0.00', '41000'),
(12, 8, 10, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(14, 10, 12, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(15, 11, 13, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(100, 49, 4, '\nDOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', '0.00', '41000'),
(18, 14, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(19, 15, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(97, 53, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(96, 52, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(24, 20, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(95, 51, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(94, 50, 0, '\\r\\nThank you for entrusting eNoah with your web technology requirements. \\r\\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(93, 50, 4, 'EMAIL ADDRESS CONFIGURATIONrnSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(88, 46, 2, '\nFurnitures', '180000.00', '12.00', '41000'),
(89, 47, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(90, 48, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(91, 49, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(265, 50, 5, 'asd fasd fasd fasdfasfd', '24.00', '2.00', '41000'),
(266, 101, 0, '\n\\r\\nEMAIL ADDRESS CONFIGURATION\\r\\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(31, 25, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(32, 26, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(87, 46, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(86, 45, 2, '\nTest  for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(36, 26, 2, '\nSHARED SSL CERTIFICATE\nAvailable as an add-on to the Power Cluster hosting package, access to our shared SSL certificate provides the ability to store part or all of a web site on one of our secure webservers and present those pages to a user and collect responses in a secure manner. SSL, or secure sockets layer, is a mechanism for web browsers to connect to web servers and encrypt the data sent in either direction for security requirements.\n\nShared SSL Certficate is charged on an annual basis.', '100.00', '0.00', '41000'),
(37, 26, 3, '\ntesting viky quote items ', '500.00', '0.00', '41000'),
(38, 25, 2, '\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', '32.00', '8.00', '41000'),
(39, 25, 3, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(40, 30, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(41, 30, 2, '\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', '0.00', '0.00', '41000'),
(42, 30, 3, '\nUSER MANAGEMENT SYSTEM (UMS)\nThis powerful system allows your to capture member registration data including name, surname, email, mobile, address, etc and build a database of registered users to your website. This is essential when unique and individual user accounts to login to special sections of a website is required or if you wish to integrate with your eCommerce website with different payment categories, ie. Wholesale, Retail, etc where the shoppers will need to login using their own user name and password to shop online.					', '2490.00', '0.00', '41000'),
(85, 45, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(84, 44, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(49, 33, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(50, 33, 2, '\nTest', '0.00', '0.00', '41000'),
(51, 33, 3, '\nTest1', '50.00', '1.00', '41000'),
(52, 33, 4, '\nPRE-PRODUCTION / PROJECT PLANNING\nProduce Wireframe + Functional Specifications documentation of all aspects of the project including:\n\n- Front-end Design & Development\n- Back-end programming and logic\n- Data Export expectations\n- Data Import/Capture expectations\n- Interactive Design and UX\n- Hosting, Maintenance + Support', '1400.00', '0.00', '41000'),
(53, 33, 5, '\nIE6 OPTIMISATION\nOptimisation of your website in this browser ensures your visitors will be able to view your website in a perfectly functional browsing experience and without any broken design elements the browser typically loads when XHTML and CSS is not optimised. This service includes optimisation for a working website in Internet Explorer 6 and does not promise to provide an exact representation of the website as it appears in other more stable browsers. Please call us on 1300 130 656 if this level of optimisation is insufficient for your needs.', '700.00', '0.00', '41000'),
(54, 33, 6, '\nSHARED SSL CERTIFICATE\nAvailable as an add-on to the Power Cluster hosting package, access to our shared SSL certificate provides the ability to store part or all of a web site on one of our secure webservers and present those pages to a user and collect responses in a secure manner. SSL, or secure sockets layer, is a mechanism for web browsers to connect to web servers and encrypt the data sent in either direction for security requirements.\n\nShared SSL Certficate is charged on an annual basis.', '100.00', '0.00', '41000'),
(55, 34, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(56, 34, 2, '\nAny Description Details', '16.00', '2.00', '41000'),
(57, 34, 3, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(58, 34, 4, '\n\nINTERACTIVE MICROSITE, HOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n100 email accounts, 5GB monthly traffic, 1 MySQL database, Statistics Report, Webmail Control Panel.\n\nMaintenance:\nDaily backups, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, CSS/XHTML minor fixes for agreed browsers.\n\nSupport:\nBusiness hours technical support, software tele-training and assistance, unlimited phone assistance with email configuration.\n\nPAID MONTHLY via Mastercard, Visa or EFT only:', '29.90', '0.00', '41000'),
(62, 35, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(60, 34, 6, '\nDOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', '0.00', '41000'),
(61, 34, 7, '\nWebflowBOS [base system 31-50 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 31-50 Users per month\n*Minimum commitment of 12 months.', '790.00', '0.00', '41000'),
(63, 36, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(64, 37, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(65, 38, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(66, 38, 2, '\nTest', '500.00', '5.00', '41000'),
(67, 38, 3, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(68, 38, 4, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(69, 38, 5, '\nV-SERIES \n[ Base System ]\nThe V-Series Website Package is our pre-developed modular technology package that provides small to medium enterprise the ability to drive their online business demands with world-class technology at a fraction of the price of custom technology. V-Series is sold to you as a pre-developed packaged functionality and you simply purchase the modules you need!... Our base system comes with a powerful Content Management System to get you started and a custom user interface design to keep you on brand with your corporate image. V-Series base system includes 7 pages + CMS.', '2990.00', '0.00', '41000'),
(70, 38, 6, '\nV-SERIES \n[ Base System ]\nThe V-Series Website Package is our pre-developed modular technology package that provides small to medium enterprise the ability to drive their online business demands with world-class technology at a fraction of the price of custom technology. V-Series is sold to you as a pre-developed packaged functionality and you simply purchase the modules you need!... Our base system comes with a powerful Content Management System to get you started and a custom user interface design to keep you on brand with your corporate image. V-Series base system includes 7 pages + CMS.', '2990.00', '0.00', '41000'),
(71, 38, 7, '\nLOGO DESIGN\n\nFrom supplied creative brief, Visiontech Digital are to research, develop and design a corporate logo for the client with consideration to both online and offline branding, target audience, industry sector and various other influences.\n\nThis quotation includes 3 individual and unique designs for your consideration and prospective approval. Upon successful approval and payment of your new logo concept the master vector files will be released.\n\n*IMPORTANT\nPrice includes 2 rounds of changes per design concept. Any additional changes will be charged out at our hourly studio rate.', '2100.00', '0.00', '41000'),
(98, 49, 2, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(99, 49, 3, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(74, 41, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(75, 41, 2, '\nGOOGLE MAPS\nIntegrated Google MAPS for website accessible via CMS control. Website administrator can simply add contact details to CMS and website will call exact Google MAP to appear via iFrame.', '175.00', '0.00', '41000'),
(77, 41, 3, '\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s', '4.00', '0.00', '41000'),
(78, 43, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(79, 43, 2, '\nLorem Ipsum is simply dummy text of the printing and typesetting industry', '24.00', '12.00', '41000'),
(80, 43, 3, '\nLorem Ipsum is simply dummy text of the printing and typesetting industry', '0.00', '1.00', '41000'),
(81, 43, 4, '\nLorem Ipsum is simply dummy text of the printing and typesetting industry', '1.00', '0.00', '41000'),
(82, 43, 5, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(83, 43, 6, '\n[ Traffic Starter ] Search Engine Optimisation (SEO)\n- 10 Key phrases \n- Key phrase research \n- Internal website audit \n- Search engine submissions for Google, Yahoo, Bing \n- Monthly ranking reports for Google, Yahoo & Bing \n- Results appear on Google within 90 days \n- Comprehensive onsite optimisation', '500.00', '0.00', '41000'),
(101, 49, 5, '\nDOMAIN NAME REDIRECTION\nFrom supplied domain names, Visiontech Digital will redirect your domain names to the desired URL for websites that have multiple domain names bringing traffic to them - 1 x domain name redirection.', '87.50', '0.00', '41000'),
(102, 49, 6, '\nDOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', '0.00', '41000'),
(103, 53, 2, '\ndfasdfasdf', '45.00', '1.00', '41000'),
(104, 53, 3, '\nasdfasd fasdf', '90.00', '2.00', '41000'),
(105, 49, 7, '\ntest item', '23.00', '1.00', '41000'),
(106, 49, 8, '\ntest item1', '46.00', '2.00', '41000'),
(107, 54, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(108, 54, 2, '\ntest', '45.00', '1.00', '41000'),
(109, 54, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(110, 54, 4, '\nPRE-PRODUCTION / PROJECT PLANNING\nProduce Wireframe + Functional Specifications documentation of all aspects of the project including:\n\n- Front-end Design & Development\n- Back-end programming and logic\n- Data Export expectations\n- Data Import/Capture expectations\n- Interactive Design and UX\n- Hosting, Maintenance + Support', '1400.00', '0.00', '41000'),
(111, 54, 5, '\nCUSTOM PROGRAMMING + DEVELOPMENT\nDevelop and programme all proposed functionality on the sitemap and ensure all areas are properly tested and debugged prior to go-live. This refers to all sections of the project, and considers all JavaScript. AJAX work specified in the functional spec along with all PHP works required to achieve objective.', '1400.00', '0.00', '41000'),
(112, 55, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(113, 55, 2, '\ntest item #1', '80.00', '4.00', '41000'),
(114, 55, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(115, 55, 4, '\nWebflowBOS [base system 1-10 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 1-10 Users per month\n*Minimum commitment of 12 months.', '490.00', '0.00', '41000'),
(116, 55, 5, '\nDOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', '0.00', '41000'),
(117, 55, 6, '\nHUBONLINE XML INTEGRATION\nConnection of selected HubOnline Data via XML feed directly into your website package for seamless integration and data consolidation. This charge does not include fees that HubOnline may charge you, the client for access to the XML data of your listings. Please discuss this with your account manager at REA/HubOnline.', '750.00', '0.00', '41000'),
(118, 56, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(119, 56, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(120, 56, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(121, 56, 4, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(122, 57, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(123, 58, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(240, 95, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(239, 94, 3, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(238, 94, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(234, 93, 2, '\nGOOGLE MAPS\nIntegrated Google MAPS for website accessible via CMS control. Website administrator can simply add contact details to CMS and website will call exact Google MAP to appear via iFrame.', '175.00', '0.00', '41000'),
(235, 93, 1, '\nFLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '700.00', '0.00', '41000'),
(236, 93, 3, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', '0.00', '41000'),
(237, 94, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(131, 60, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(132, 60, 2, '\nasdf asdf asdf asdfasdf', '108.00', '12.00', '41000'),
(133, 60, 3, '\nFLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '650.00', '0.00', '41000'),
(134, 60, 4, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '43.75', '0.00', '41000'),
(135, 60, 5, '\nWebflowBOS [base system 1-10 Users]\n\nWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.\n\n*Accounting Module + MYOB Integration not included in base system, call for details.\n\n- User Management Module\n- Dashboard Module\n- Tasks Module\n- User Logging Module\n- Projects Module\n\n@ 1-10 Users per month\n*Minimum commitment of 12 months.', '490.00', '0.00', '41000'),
(136, 60, 6, '\nHOSTING + MAINTENANCE + SUPPORT\n\nHosting:\n1GB Storage, 100GB Downloads, 10GB Uploads, PHP 5 + Apache 2, MySQL database, Unlimited email and Load-balanced servers.\n\nMaintenance:\nHourly backups, Free instant restores, Spam filtering, Email virus blocking, Performance Compression, Reliable Session Management, CSS/XHTML fixes for new browser releases.\n\nSupport:\nBusiness hours technical support, software tele-training and assistance, unlimited assistance with POP/IMAP/WEB email setup (phone service available only).\n\nPAID MONTHLY via Mastercard, Visa or EFT only:', '139.90', '0.00', '41000'),
(137, 61, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(138, 61, 2, '\ntest cart', '300.00', '5.00', '41000'),
(139, 61, 3, '\nSINGLE PAGE HOSTING (used with parked domains)\neNoah iSolution will host a single page on a supplied domain name. \nGeneral purpose of a single page on a parked domain is to generate leads to a specified website.\n\nSingle page hosting is billed annually.', '100.00', '0.00', '41000'),
(140, 61, 4, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', '0.00', '41000'),
(141, 61, 5, '\nPRINTABLE BROCHURE\nVisitors can print from a ''printer friendly'' page from your property detail page which will compose the property data in an A4 friendly format.', '175.00', '0.00', '41000'),
(142, 61, 6, '\nEMAIL MARKETING\nNewsletterPRO Premium Edition\nA professional and value packed email marketing solution designed to help you manage customers and communicate using content rich html based email (SMS option available with Premium Edition SMS charges apply) NewsletterPRO Premium Edition provides Opt-in / Opt-out SPAM Act compliance and allows you to send up to 12,000 emails per day!\n\n*$350 once off setup and installation, including template customisation\n**$49.90 per month direct debit from valid credit card, minimum commitment of 12 months.', '350.00', '0.00', '41000'),
(143, 61, 7, '\nGOOGLE ANALYTICS\nConnection and installation of Google Analytics to live website providing the tools for the client to access and monitor traffic and overall website activity.					', '175.00', '0.00', '41000'),
(144, 61, 8, '\nDOMAIN NAME DELEGATION\nFrom supplied domain name (client to supply registry key and/or domain name password) eNoah iSolution will delegate your domain name to your new host server - 1 x domain name delegation.', '87.50', '0.00', '41000'),
(231, 92, 3, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', '0.00', '41000'),
(229, 92, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(230, 92, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(227, 91, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(151, 63, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(152, 63, 2, '\nasdfasdf', '68.00', '2.00', '41000'),
(153, 63, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(154, 63, 4, '\nGOOGLE MAPS\nIntegrated Google MAPS for website accessible via CMS control. Website administrator can simply add contact details to CMS and website will call exact Google MAP to appear via iFrame.', '175.00', '0.00', '41000'),
(155, 63, 5, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', '0.00', '41000'),
(156, 63, 6, '\nV-SERIES \n[ Base System ]\nThe V-Series Website Package is our pre-developed modular technology package that provides small to medium enterprise the ability to drive their online business demands with world-class technology at a fraction of the price of custom technology. V-Series is sold to you as a pre-developed packaged functionality and you simply purchase the modules you need!... Our base system comes with a powerful Content Management System to get you started and a custom user interface design to keep you on brand with your corporate image. V-Series base system includes 7 pages + CMS.', '2990.00', '0.00', '41000'),
(157, 64, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(158, 64, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(159, 64, 3, '\nFLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '700.00', '0.00', '41000'),
(160, 64, 4, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '700.00', '0.00', '41000'),
(161, 64, 5, '\nEMAIL MARKETING\nNewsletterPRO Premium Edition\nA professional and value packed email marketing solution designed to help you manage customers and communicate using content rich html based email (SMS option available with Premium Edition SMS charges apply) NewsletterPRO Premium Edition provides Opt-in / Opt-out SPAM Act compliance and allows you to send up to 12,000 emails per day!\n\n*$350 once off setup and installation, including template customisation\n**$49.90 per month direct debit from valid credit card, minimum commitment of 12 months.', '350.00', '0.00', '41000'),
(162, 64, 6, '\nE-COMMERCE\nV-Shop Online Shopping System\nIf you are selling a product or service online and are seeking an easy to use eCommerce solution, then V-Shop is the choice for your business. V-Shop easily connects to an existing static website and takes you to the eCommerce arena accepting the entire transaction from your website.\nFeatures Included:\n- Content Management System\n- Product Catalogue\n- Shopping Cart facility + Checkout\n- Connection to PayPal directly from Checkout\n- Setup, installation and customisation\n- Business hours technical support\n- 1 hour of in-studio training', '1990.00', '0.00', '41000'),
(244, 93, 6, '\\nFLASH BILLBOARD\\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space. as per', '700.00', '0.00', '41000'),
(257, 99, 1, '\nyrdf sdfg sdfg sdfg', '40.00', '2.00', '41000'),
(258, 99, 2, '\n\\nGOOGLE ADWORDS\\nSetup and configuration of a Google AdWords \\''pay-per-click\\'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(169, 66, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(170, 66, 1, '\n*** WHOLESALE CLIENT DISCOUNT ***\nIn an effort to develop and maintain a mutually beneficial business relationship, eNoah iSolution is proud to offer you a generous 15% wholesale/resellers discount on the above mentioned web technology.', '0.00', '0.00', '41000'),
(171, 66, 4, '\nDOMAIN NAME REGISTRATION\nOn behalf of the client and from supplied business details including your official trading name and ABN, we will register the domain name of your choice for your websites address. Domain names are registered for 24 months and are required to be renewed thereafter. It is your responsibility to ensure your domain name is always registered and has not lapsed.', '82.50', '0.00', '41000'),
(172, 66, 3, '\nWIREFRAME + INTERFACE DESIGN\nWe will design a GUI (Graphical User Interface) and establish the new web page layout rules for master content pages and subsequent information pages. A new primary navigation panel and information architecture will be considered during this process. eNoah iSolution to establish a series of layout page designs which will form the basis of the remainder of the website and house the entire website content thereafter.', '2800.00', '0.00', '41000'),
(173, 66, 2, '\nLOGO DESIGN\n\nFrom supplied creative brief, eNoah iSolution are to research, develop and design a corporate logo for the client with consideration to both online and offline branding, target audience, industry sector and various other influences.\n\nThis quotation includes 3 individual and unique designs for your consideration and prospective approval. Upon successful approval and payment of your new logo concept the master vector files will be released.\n\n*IMPORTANT\nPrice includes 2 rounds of changes per design concept. Any additional changes will be charged out at our hourly studio rate.', '2100.00', '0.00', '41000'),
(174, 67, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(175, 67, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '440.75', '0.00', '41000'),
(176, 67, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '1750.00', '0.00', '41000'),
(177, 67, 4, '\nJAVASCRIPT BILLBOARD\nJavaScript slideshow to appear on the home page as a billboard where selected images (up to 7 images) can be rotated in a slideshow fashion with smooth, animated transitions between each image.', '350.00', '0.00', '41000'),
(178, 67, 5, '\nFLASH BILLBOARD\nA 20 second Flash animated billboard to appear on the home page where important messages supported by imagery can be communicated to the visitor in the form of an engaging animated home page presentation. Client to supply imagery and copy along with an idea of storyboard to fill the 20 sec space.', '700.00', '0.00', '41000'),
(179, 67, 6, '\nCMS PROGRAMMING \nWe will connect 8 page(s) to our WebPublisherCMS Content Management System allowing the text and image content of those pages to be editable by the client. ', '7000.00', '0.00', '41000'),
(180, 67, 7, '\nPRINTABLE BROCHURE\nVisitors can print from a ''printer friendly'' page from your property detail page which will compose the property data in an A4 friendly format.', '175.00', '0.00', '41000'),
(181, 67, 8, '\nEMAIL-A-FRIEND\nVisitors can email a page from your website to their friends and colleagues allowing for external traffic to head into your website increasing site traffic and website activity.', '175.00', '0.00', '41000'),
(182, 67, 9, '\nFLASH INTERACTIVE NAVIGATION\nDesign and development of a Flash animated/interactive primary navigation panel to reside on all pages throughout the website and allow visitors to navigate their way through to the major sections of the website.', '700.00', '0.00', '41000'),
(183, 68, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(184, 69, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(185, 70, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(186, 71, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(187, 72, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(188, 73, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(189, 74, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(242, 93, 4, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(191, 76, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(192, 76, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(193, 76, 3, '\nGOOGLE ADWORDS\nSetup and configuration of a Google AdWords ''pay-per-click'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(194, 77, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(195, 78, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(196, 79, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(197, 80, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(199, 82, 2, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(200, 82, 0, '\nXHTML / CSS CODING\nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):', '0.00', '0.00', '41000'),
(201, 82, 1, 'asdf', '350.00', '0.00', '41000'),
(202, 83, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(203, 84, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(204, 84, 1, 'INTERFACE DESIGN\r\nWe will design a GUI (Graphical User Interface) and establish the new web page layout rules for master content pages and subsequent information pages. A new primary navigation panel and information architecture will be considered during this process. eNoah  iSolution to establish a minimum of 3 master layout page designs including: home content layout, master content layout #1, master content layout #2 which will form the basis of the remainder of the website and house the entire website content thereafter.', '2800.00', '0.00', '41000'),
(205, 84, 2, '\nXHTML / CSS CODING\nFrom the approved design concepts for the user interface and master content pages, we will code in standards compliant XHTML/CSS the following page(s):', '0.00', '0.00', '41000'),
(207, 84, 3, 'asdf', '350.00', '0.00', '41000'),
(210, 84, 4, '\r\nCMS PROGRAMMING\r\nWe will connect 4 page(s) to our WebPublisherCMS allowing the text and image content of those pages editable by the client.', '18.00', '0.00', '41000'),
(225, 89, 1, '\\nEMAIL ADDRESS CONFIGURATION\\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '45.00', '0.00', '41000'),
(212, 84, 5, '\r\nEMAIL MARKETING\r\nNewsletterPRO Email Marketing System \r\nAn affordable and value for money marketing solution for just over $1 dollar a day. Communicate directly to your entire client base with customised, content rich html based email. With Opt-in / Opt-out SPAM Act compliance, NewsletterPRO is the easiest way to send content rich html newsletters and promotional material to your customers, when you want... where you want... how you want!\r\n\r\n*$175.00 once off setup and installation, including template customisation \r\n**$39.95 per month direct debit from valid credit card \r\n***minimum commitment of 24 months', '175.00', '0.00', '41000'),
(215, 85, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(216, 85, 2, '\nEMAIL ADDRESS CONFIGURATION\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(217, 86, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(218, 87, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(219, 88, 1, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(220, 89, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(221, 90, 0, '\nThank you for entrusting eNoah  iSolution with your web technology requirements.\nPlease see below an itemised breakdown of our service offering to you:', '0.00', '0.00', '41000'),
(222, 90, 1, '\nE-COMMERCE\nV-Shop Online Shopping System\nIf you are selling a product or service online and are seeking an easy to use eCommerce solution, then V-Shop is the choice for your business. V-Shop easily connects to an existing static website and takes you to the eCommerce arena accepting the entire transaction from your website.\nFeatures Included:\n- Content Management System\n- Product Catalogue\n- Shopping Cart facility + Checkout\n- Connection to PayPal directly from Checkout\n- Setup, installation and customisation\n- Business hours technical support\n- 1 hour of in-studio training', '1990.00', '0.00', '41000'),
(223, 90, 3, '\n\nINTERACTIVE MICRO-SITE PACKAGE\nPerfect for the small business start-up, our Interactive Micro-site package provides every detail you need to launch into your first website without compromising on quality. Our package provides you with a great design framework to work within; our powerful signature application WebPublisherCMS drives all 6 pages. \n\nThe following inclusions are available with this package:-\n\n- Custom design elements on fixed grid\n- 6 x pages all driven by our powerful WebPublisherCMS\n- Image gallery and Contact form (2 of the 6 pages)\n- Interactive JavaScript Slides', '1990.00', '0.00', '41000'),
(224, 90, 2, '\ntest', '90.00', '2.00', '41000'),
(247, 83, 1, '\n***** CLIENT DISCOUNT *****\nIn an effort to nurture solid business relations with you, eNoah iSolution is proud to offer a generous 10% discount on the above mentioned services.										', '10.00', '0.00', '41000'),
(267, 101, 1, '\n\\r\\nPRE-PRODUCTION / PROJECT PLANNING\\r\\nProduce Wireframe + Functional Specifications documentation of all aspects of the project including:\\r\\n\\r\\n- Front-end Design & Development\\r\\n- Back-end programming and logic\\r\\n- Data Export expectations\\r\\n- Data Import/Capture expectations\\r\\n- Interactive Design and UX\\r\\n- Hosting, Maintenance + Support', '1400.00', '0.00', '41000'),
(261, 50, 1, '\n\\r\\nGOOGLE ADWORDS\\r\\nSetup and configuration of a Google AdWords \\''pay-per-click\\'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(262, 50, 6, '\\r\\nEMAIL ADDRESS CONFIGURATIONrnSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(271, 77, 2, '\n\\r\\nWIREFRAME + INTERFACE DESIGN\\r\\nWe will design a GUI (Graphical User Interface) and establish the new web page layout rules for master content pages and subsequent information pages. A new primary navigation panel and information architecture will be considered during this process. eNoah iSolution to establish a series of layout page designs which will form the basis of the remainder of the website and house the entire website content thereafter.', '2800.00', '0.00', '41000');
INSERT INTO `crms_items` (`itemid`, `jobid_fk`, `item_position`, `item_desc`, `item_price`, `hours`, `ledger_code`) VALUES
(268, 101, 2, 'WebflowBOS [base system 11-30 Users]rnrnWebflowBOS is a web-based business operations system that brings you the power to run, manage and measure your entire operation from anywhere in the world. Manage staff, customers, projects, tasks, accounts (MYOB integrated)* and more with WebflowBOS and take control over your business.rnrn*Accounting Module + MYOB Integration not included in base system, call for details.rnrn- User Management Modulern- Dashboard Modulern- Tasks Modulern- User Logging Modulern- Projects Modulernrn@ 11-30 Users per monthrn*Minimum commitment of 12 months.', '690.00', '0.00', '41000'),
(269, 101, 4, '\n\\r\\nEMAIL ADDRESS CONFIGURATION\\r\\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(270, 101, 3, '\n\\r\\nGOOGLE ADWORDS\\r\\nSetup and configuration of a Google AdWords \\''pay-per-click\\'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000'),
(272, 102, 0, '\n\\r\\nEMAIL ADDRESS CONFIGURATION\\r\\nSetup an email account for the client using their current domain name. Assist the client in configuring the POP mail settings and providing access to the WEBmail version of this new email account.', '44.75', '0.00', '41000'),
(275, 77, 1, 'TIMEFRAMEWe estimate completion of the above project would take place within a time frame of 6 weeks providing that all content and client cooperation is in place.', '0.00', '0.00', '41000'),
(276, 102, 1, 'GOOGLE ADWORDSSetup and configuration of a Google AdWords \\''pay-per-click\\'' sponsored ad. (Client to provide a valid credit card for this service along with ad copy).', '175.00', '0.00', '41000');

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_services`
--

CREATE TABLE IF NOT EXISTS `crms_lead_services` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(150) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

--
-- Dumping data for table `crms_lead_services`
--

INSERT INTO `crms_lead_services` (`cid`, `category`, `status`) VALUES
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
(47, 'Mobilty Services', 1),
(48, 'test services', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `crms_job_urls`
--

INSERT INTO `crms_job_urls` (`urlid`, `jobid_fk`, `userid_fk`, `url`, `content`, `date`) VALUES
(4, 44, 59, 'http://google.com', '', '2013-03-29 18:31:10'),
(3, 25, 59, 'http://google.com', '', '2013-03-29 11:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `crms_leads`
--

CREATE TABLE IF NOT EXISTS `crms_leads` (
  `lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_title` varchar(200) NOT NULL,
  `job_desc` text,
  `lead_service` tinyint(4) DEFAULT NULL,
  `lead_source` int(5) DEFAULT NULL,
  `lead_assign` int(5) DEFAULT NULL,
  `expect_worth_id` int(4) NOT NULL,
  `expect_worth_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `actual_worth_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `invoice_no` varchar(40) DEFAULT NULL,
  `custid_fk` int(11) NOT NULL,
  `date_quoted` datetime DEFAULT NULL,
  `date_invoiced` datetime DEFAULT NULL,
  `lead_stage` tinyint(4) DEFAULT '1',
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
  PRIMARY KEY (`lead_id`),
  KEY `custid_fk` (`custid_fk`),
  KEY `assigned_to` (`assigned_to`),
  KEY `belong_to` (`belong_to`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- Dumping data for table `crms_leads`
--

INSERT INTO `crms_leads` (`lead_id`, `lead_title`, `job_desc`, `lead_service`, `lead_source`, `lead_assign`, `expect_worth_id`, `expect_worth_amount`, `actual_worth_amount`, `invoice_no`, `custid_fk`, `date_quoted`, `date_invoiced`, `lead_stage`, `complete_status`, `assigned_to`, `pjt_id`, `date_start`, `date_due`, `actual_date_start`, `actual_date_due`, `date_created`, `date_modified`, `proposal_expected_date`, `proposal_adjusted_date`, `created_by`, `modified_by`, `account_manager`, `belong_to`, `division`, `payment_terms`, `log_view_status`, `lead_status`, `pjt_status`, `lead_indicator`, `lead_hold_reason`) VALUES
(52, 'HH', NULL, 7, 3, 149, 1, 3000.00, 0.00, '00052', 52, NULL, '2013-09-04 19:13:23', 14, 90, 150, NULL, '2013-05-07 00:00:00', '2013-05-08 00:00:00', '2013-05-07 00:00:00', '2013-05-08 00:00:00', '2013-04-01 15:48:53', '2013-09-12 14:05:28', '2013-04-25 00:00:00', NULL, 161, 59, NULL, '161', '1', 0, ':161:59:139:158', 4, 4, 'HOT', ''),
(60, 'asdf asdf asdf asdf asdf', NULL, 1, 1, 163, 1, 10000.00, 1381.65, '00060', 84, NULL, NULL, 5, NULL, NULL, NULL, '2013-10-16 00:00:00', NULL, NULL, NULL, '2013-04-24 14:44:33', '2013-10-25 13:55:40', NULL, '2013-04-24 19:35:42', 139, 59, NULL, '139', '2', 0, '59:158:139:167', 3, 0, 'HOT', 'Other reasons'),
(53, 'HooperHolmes', NULL, 12, 3, 163, 1, 10000.00, 135.00, '00053', 67, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-04-02 12:03:36', '2013-11-04 16:47:08', '2013-04-17 00:00:00', '2013-09-11 17:35:40', 161, 59, NULL, '155', '1', 0, '59:161:158:165:159:135:139', 3, 0, 'HOT', ''),
(56, 'test ramji', NULL, 7, 5, 158, 3, 10000.00, 0.00, '00056', 45, NULL, '2013-04-23 16:59:06', 14, 90, 173, NULL, NULL, NULL, NULL, NULL, '2013-04-17 17:33:56', '2013-11-06 14:46:19', '2013-04-26 00:00:00', NULL, 59, 59, NULL, '59', '1', 0, '59', 4, 2, 'COLD', NULL),
(50, 'HooperHolmes', NULL, 2, 6, 163, 1, 1000.00, 43.75, '00050', 67, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-03-29 14:52:17', '2013-10-31 16:54:31', '2013-04-09 00:00:00', '2013-04-16 18:36:11', 161, 59, NULL, '155', '1', 0, '59:161:139:165:135:158', 1, 0, 'HOT', 'payment delayed'),
(49, 'IT Testing & Design', NULL, 12, 3, 59, 1, 1000.00, 646.25, '00049', 67, NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-03-29 14:34:54', '2013-09-12 14:25:45', NULL, '2013-04-16 18:59:07', 161, 59, NULL, '155', '1', 0, '59:165:139:161', 1, 0, 'WARM', ''),
(48, 'IT Project', NULL, 1, 6, 161, 1, 2000.00, 0.00, '00048', 67, NULL, '2013-04-02 19:05:12', 15, 40, 150, 'HH2', '2013-03-28 00:00:00', '2013-05-15 00:00:00', '2013-03-29 00:00:00', '2013-03-30 00:00:00', '2013-03-29 14:16:05', '2013-09-11 17:09:28', NULL, NULL, 161, 161, NULL, '161', '1', 0, '161:59:170', 4, 1, 'HOT', NULL),
(25, 'test lead cum test project test lead cum test ', NULL, 2, 9, 139, 2, 12.00, 0.00, '00025', 18, NULL, '2013-08-28 14:46:01', 14, 90, 139, 'TETPJT-001-099', '2013-02-05 00:00:00', '2013-05-08 00:00:00', '2013-02-05 00:00:00', '2013-05-10 00:00:00', '2013-02-01 16:09:06', '2013-10-24 12:08:45', NULL, NULL, 139, 139, NULL, '139', '1', 1, '156:59:139', 4, 2, 'COLD', NULL),
(44, 'test project', NULL, 2, 9, 139, 1, 100.00, 0.00, '00044', 12, NULL, '2013-04-19 18:20:48', 14, 80, 150, 'HH', '2013-04-10 00:00:00', '2013-04-03 00:00:00', '2013-04-01 00:00:00', '2013-04-02 00:00:00', '2013-03-25 19:37:37', '2013-09-11 17:09:28', NULL, NULL, 139, 139, NULL, '139', '1', 1, '59:139', 4, 1, 'COLD', NULL),
(47, 'Pet Finder', NULL, 2, 9, 59, 4, 15000.00, 0.00, '00047', 66, NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-02-01 16:43:11', '2013-09-12 14:25:45', '2013-04-18 00:00:00', NULL, 160, 59, NULL, '160', '1', 0, ':160:59:165:135:158:139', 1, 0, 'COLD', ''),
(61, 'lead for shopping cart', NULL, 8, 7, 161, 3, 1500.00, 1220.00, '00061', 85, NULL, NULL, 13, 10, 158, 'sriram123', '2013-10-23 00:00:00', '2013-12-24 00:00:00', NULL, NULL, '2013-04-26 11:07:54', '2013-10-23 19:54:21', NULL, NULL, 155, 155, NULL, '155', '2', 1, ':155:59', 4, 1, 'COLD', NULL),
(36, 'sujaj', NULL, 2, 9, 139, 1, 10.00, 0.00, '00036', 51, NULL, '2013-03-29 11:59:10', 15, NULL, NULL, 'samp 08', NULL, NULL, NULL, NULL, '2013-02-21 14:42:42', '2013-10-11 14:29:07', NULL, NULL, 59, 59, NULL, '59', '1', 0, ':59:155:139:152', 4, 3, 'COLD', NULL),
(37, 'sai enter', NULL, 2, 9, 59, 1, 10.00, 0.00, '00037', 51, NULL, '2013-06-18 18:10:22', 15, 90, 150, NULL, '2013-05-01 00:00:00', '2013-05-01 00:00:00', '2013-05-02 00:00:00', '2013-05-04 00:00:00', NULL, '2013-09-11 17:09:28', NULL, NULL, 59, 59, NULL, '59', '1', 1, '59', 4, 1, 'COLD', NULL),
(38, 'Test', NULL, 10, 2, 148, 1, 3463.00, 0.00, '00038', 14, NULL, '2013-04-01 11:10:04', 15, 70, NULL, NULL, NULL, NULL, NULL, NULL, '2013-02-22 12:10:11', '2013-09-11 17:09:28', NULL, NULL, 59, 59, NULL, '59', '3', 0, ':59:156:139', 4, 1, 'COLD', NULL),
(45, 'crm project', NULL, 2, 9, 139, 2, 100.00, 0.00, '00045', 14, NULL, '2013-09-04 19:13:01', 13, NULL, 150, NULL, '2013-04-17 00:00:00', '2013-04-30 00:00:00', '2013-04-18 00:00:00', '2013-05-03 00:00:00', '2013-03-26 10:48:10', '2013-09-12 14:41:10', NULL, NULL, 139, 139, NULL, '139', '1', 1, ':139:59:158:135:170', 4, 1, 'COLD', NULL),
(55, 'lead for new n', NULL, 4, 4, 165, 5, 25000.00, 0.00, '00055', 66, NULL, '2013-09-04 19:14:08', 15, 100, 158, 'TSTPJT-001-100', '2013-03-01 00:00:00', '2013-03-04 00:00:00', '2013-03-02 00:00:00', '2013-03-05 00:00:00', '2013-04-17 13:13:36', '2013-09-12 14:45:25', '2013-04-16 00:00:00', NULL, 165, 165, NULL, '165', '1', 1, '59', 4, 2, 'COLD', NULL),
(54, 'test ramji', NULL, 10, 3, 155, 3, 1000.00, 3020.00, '00054', 14, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-04-16 19:15:23', '2013-09-23 15:55:42', '2013-09-19 00:00:00', '2013-07-26 16:10:35', 139, 59, NULL, '163', '2', 0, ':139:59:165:135:158:168:170:161', 1, 0, 'WARM', ''),
(63, 'hi test leadasdf ', NULL, 4, 3, 163, 3, 1231243.00, 75000.00, '00063', 94, NULL, '2013-09-02 19:38:17', 13, 90, 158, 'TST1458PJTCMP', '2013-09-01 00:00:00', '2013-10-02 00:00:00', NULL, NULL, '2013-05-09 12:21:24', '2013-10-01 14:45:14', '2013-06-27 00:00:00', '2013-06-27 15:25:33', 139, 59, NULL, '139', '4', 1, ':59:170:158', 4, 2, 'HOT', ''),
(64, 'test customer lead', NULL, 2, 6, 158, 1, 5000.00, 3784.75, '00064', 18, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-10 12:20:17', '2013-09-27 16:48:05', '2013-09-28 00:00:00', '2013-07-17 15:52:30', 155, 155, NULL, '155', '3', 0, ':161:59:155:158', 1, 0, 'COLD', ''),
(66, 'lead frm dinesh', NULL, 4, 5, 135, 4, 200000.00, 4982.50, '00066', 44, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-11 17:03:18', '2013-09-12 14:25:45', NULL, '2013-07-18 15:29:20', 59, 59, NULL, '59', '3', 0, ':59', 1, 0, 'HOT', ''),
(67, 'surya lead 4 test', NULL, 4, 7, 160, 3, 15000.00, 11291.75, '00067', 66, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-15 17:26:25', '2013-09-12 14:41:10', '2013-07-17 00:00:00', '2013-07-17 18:55:57', 59, 59, NULL, '59', '2', 0, ':59', 4, 1, 'HOT', ''),
(68, 'Qad Process', NULL, 3, 4, 167, 2, 12500.00, 0.00, '00068', 88, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-18 21:13:42', '2013-09-12 14:25:45', NULL, NULL, 139, 59, NULL, '139', '4', 0, ':139:167:59', 1, 0, 'COLD', ''),
(69, 'PHP Development', NULL, 11, 4, 163, 4, 12000.00, 0.00, '00069', 98, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-18 21:16:17', '2013-09-12 14:25:45', NULL, NULL, 158, 59, NULL, '158', '2', 0, ':59:158', 1, 0, 'COLD', ''),
(70, 'Testing Application', NULL, 47, 9, 139, 3, 35000.00, 0.00, '00070', 66, NULL, NULL, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-18 21:19:14', '2013-11-05 21:38:58', '2013-09-25 00:00:00', '2013-11-05 21:38:43', 160, 59, NULL, '160', '2', 0, ':170:59', 4, 1, 'WARM', ''),
(71, 'Testing Lead for Dashboard', NULL, 5, 7, 161, 1, 10000.00, 0.00, '00071', 85, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-29 18:01:40', '2013-09-12 14:25:45', '2013-08-22 00:00:00', NULL, 158, 158, NULL, '158', '3', 0, ':158:168:59', 1, 0, 'HOT', NULL),
(72, 'Leads for readings', NULL, 8, 9, 172, 2, 500000.00, 0.00, '00072', 102, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-29 19:27:46', '2013-09-12 14:46:28', NULL, NULL, 158, 59, NULL, '158', '4', 0, ':59:168', 4, 3, 'HOT', ''),
(73, 'Lead for Jukebox', NULL, 11, 7, 173, 2, 150000.00, 0.00, '00073', 103, NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-29 19:35:17', '2013-10-17 10:49:54', '2013-09-25 00:00:00', '2013-09-23 15:29:27', 59, 59, NULL, '59', '3', 0, '59', 4, 0, 'COLD', ''),
(74, 'Leads for shankar-1', NULL, 8, 3, 172, 1, 10000.00, 0.00, '00074', 103, NULL, NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-07-30 17:18:12', '2013-09-24 15:52:53', '2013-09-25 00:00:00', NULL, 172, 59, NULL, '172', '3', 0, ':172:59:173', 1, 0, 'COLD', ''),
(76, 'mums shopping cart paypal extension', NULL, 40, 6, 174, 3, 120000.00, 219.75, '00076', 85, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-06 11:04:06', '2013-09-16 12:59:52', '2013-08-22 00:00:00', '2013-08-07 20:36:09', 59, 59, NULL, '59', '2', 0, ':59:158', 4, 1, 'HOT', ''),
(77, 'test leads for mid client', NULL, 12, 2, 163, 2, 10222.00, 0.00, '00077', 95, NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-07 13:49:16', '2013-11-06 09:36:29', '2013-08-08 00:00:00', '2013-11-06 09:36:29', 59, 59, NULL, '139', '1', 0, ':59', 1, 0, 'COLD', ''),
(78, 'VVM Drupal site Leads', NULL, 3, 3, 149, 2, 1200.00, 0.00, '00078', 46, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-07 20:50:00', '2013-10-25 15:16:07', '2013-09-25 00:00:00', '2013-10-25 15:16:07', 59, 59, NULL, '135', '1', 0, ':59', 4, 0, 'HOT', 'Client not responsed'),
(79, 'test asdfa sdfa sdfasdf', NULL, 40, 2, 158, 1, 1220.00, 100.00, '00079', 18, NULL, NULL, 13, 20, 173, 'test1234pjt123', '2013-11-01 00:00:00', '2013-11-27 00:00:00', '2013-11-25 00:00:00', NULL, '2013-08-14 15:01:55', '2013-11-04 12:35:25', '2013-08-15 00:00:00', NULL, 59, 59, NULL, '59', '2', 1, '59', 4, 2, 'HOT', NULL),
(80, 'test readings leads', NULL, 14, 10, 158, 2, 15000.00, 12250.00, '00080', 102, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-26 18:03:13', '2013-09-12 14:41:10', '2013-09-04 00:00:00', '2013-09-05 17:43:49', 59, 59, NULL, '59', '4', 0, ':59', 4, 1, 'HOT', ''),
(82, 'ta sdfas asdf asdf', NULL, 1, 4, 158, 2, 0.00, 350.00, '00082', 85, NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-27 15:26:20', '2013-11-06 15:17:23', '2013-11-07 00:00:00', '2013-09-05 18:08:18', 59, 59, NULL, '158', '1', 0, '59', 4, 0, 'HOT', ''),
(83, 'test govind', NULL, 40, 5, 163, 1, 12000.00, 11000.00, '00083', 84, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-27 15:27:57', '2013-10-23 15:53:02', '2013-08-30 00:00:00', '2013-10-23 15:41:54', 59, 59, NULL, '59', '2', 0, ':59', 4, 1, 'WARM', ''),
(84, 'tstst', NULL, 1, 2, 139, 3, 12000.00, 11500.00, '00084', 45, NULL, NULL, 3, NULL, 139, NULL, NULL, NULL, NULL, NULL, '2013-08-27 15:37:47', '2013-10-11 15:13:33', '2013-08-31 00:00:00', '2013-09-19 16:55:20', 59, 59, NULL, '59', '2', 1, ':59', 4, 1, 'WARM', ''),
(85, 'test girp', NULL, 5, 6, 170, 3, 12000.00, 0.00, '00085', 105, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-27 15:49:43', '2013-10-31 18:29:41', '2013-09-26 00:00:00', '2013-09-24 15:11:04', 59, 59, NULL, '157', '2', 0, '59', 1, 0, 'HOT', ''),
(86, 'tstst', NULL, 5, 3, 163, 2, 12005.00, 0.00, '00086', 106, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-27 15:57:57', '2013-09-23 15:50:47', '2013-09-20 00:00:00', NULL, 59, 59, NULL, '59', '2', 0, ':59', 1, 0, 'WARM', NULL),
(87, 'test', NULL, 1, 3, 163, 2, 120050.00, 12600.00, '00087', 51, NULL, '2013-09-05 18:22:05', 15, NULL, NULL, 'SUJECON45OP56', '2013-09-01 00:00:00', '2013-11-01 00:00:00', '2013-09-01 00:00:00', '2013-10-01 00:00:00', '2013-08-27 15:58:48', '2013-10-25 15:04:13', '2013-09-19 00:00:00', '2013-09-04 17:28:03', 59, 59, NULL, '59', '1', 0, ':59', 4, 1, 'WARM', ''),
(88, 'Lead for Outsourcing.', NULL, 40, 4, 158, 5, 200000.00, 180000.00, '00088', 105, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-27 16:17:08', '2013-10-23 15:18:36', '2013-10-10 00:00:00', '2013-09-27 12:51:37', 59, 59, NULL, '157', '2', 0, ':59:158', 4, 0, 'WARM', ''),
(89, 'Sample Lead', NULL, 2, 2, 158, 5, 1000.00, 44.75, '00089', 18, NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-08-28 15:13:57', '2013-10-22 18:09:08', '2013-10-04 00:00:00', '2013-09-06 17:35:32', 59, 59, NULL, '59', '2', 0, ':59:158', 1, 0, 'HOT', ''),
(90, 'Test Lead', NULL, 5, 5, 158, 2, 12000.00, 4065.00, '00090', 92, NULL, NULL, 13, NULL, NULL, 'LDTST009886', NULL, NULL, NULL, NULL, '2013-08-29 11:12:44', '2013-09-12 14:42:34', '2013-08-31 00:00:00', '2013-08-29 11:39:53', 59, 59, NULL, '59', '4', 1, '59', 4, 1, 'WARM', ''),
(91, 'Sandal powder lead', NULL, 7, 6, 158, 2, 15000.00, 4070.00, '00091', 63, NULL, NULL, 1, NULL, 172, NULL, NULL, NULL, NULL, NULL, '2013-09-12 14:57:37', '2013-09-24 14:54:18', '2013-09-28 00:00:00', NULL, 59, 59, NULL, '59', '4', 0, ':59:158', 4, 1, 'WARM', ''),
(92, 'testing', NULL, 1, 7, 139, 5, 200000.00, 400.00, '00092', 32, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-09-16 12:32:16', '2013-09-16 19:23:36', '2013-09-20 00:00:00', '2013-09-16 12:47:06', 59, 59, NULL, '59', '1', 0, ':59', 4, 1, 'WARM', ''),
(93, 'Testing Lead for SSM Gp', NULL, 8, 1, 174, 4, 34000.00, 40000.00, '00093', 108, NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-09-16 13:39:47', '2013-10-31 17:20:42', '2013-10-23 00:00:00', '2013-09-16 13:41:32', 158, 59, NULL, '158', '3', 0, '59', 1, 0, 'WARM', ''),
(94, 'Anbu Testing lead', NULL, 1, 1, 163, 1, 4500.00, 4001.00, '00094', 109, NULL, NULL, 13, 20, NULL, 'PJT450OP78', NULL, NULL, NULL, NULL, '2013-09-16 19:25:51', '2013-10-29 21:17:05', '2013-09-28 00:00:00', '2013-09-16 19:26:20', 173, 173, NULL, '173', '3', 0, ':173:59', 4, 1, 'HOT', ''),
(95, 'fan lead', NULL, 7, 9, 163, 5, 45000.00, 45010.00, '00095', 97, NULL, NULL, 2, 30, 173, 'PJT450OU79@#!@1', '2013-10-17 00:00:00', '2013-10-17 00:00:00', '2013-10-30 00:00:00', '2013-10-31 00:00:00', '2013-09-20 15:34:19', '2013-10-28 19:31:08', '2013-09-27 00:00:00', '2013-09-20 15:35:58', 59, 59, NULL, '59', '1', 1, '59', 4, 1, 'WARM', ''),
(98, 'tttttttt', NULL, 3, 2, 174, 2, 2300.00, 0.00, '00098', 14, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-10-29 13:47:58', '2013-11-18 13:02:21', '2013-10-31 00:00:00', '2013-10-29 20:53:48', 59, 59, NULL, '158', '4', 0, '59:189', 1, 0, 'WARM', ''),
(99, 'My oct 10 lead', NULL, 14, 6, 139, 3, 1200.00, 0.00, '00099', 97, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-10-29 13:50:01', '2013-11-18 14:32:26', '2013-10-31 00:00:00', '2013-10-29 16:27:11', 59, 59, NULL, '159', '2', 0, ':59', 1, 0, 'HOT', ''),
(100, 'eda dasd asdf asdf', NULL, 14, 4, 163, 3, 12000.00, 0.00, '00100', 84, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-10-29 16:21:57', '2013-10-29 16:25:52', '2013-10-30 00:00:00', NULL, 59, 59, NULL, '59', '3', 0, ':59', 1, 0, 'WARM', NULL),
(101, 'test lead for testing', NULL, 3, 3, 59, 2, 12000.00, 1500.00, '00101', 108, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-10-31 18:13:51', '2013-11-06 14:49:18', '2013-11-20 00:00:00', '2013-11-06 14:49:18', 59, 59, NULL, '59', '1', 0, '59', 2, 0, 'WARM', ''),
(102, 'nteger ultrices bibendum gravida. Pellentesque quis tortor urna. Nulla vitae posuere nulla. Vestibulum tempus blandit ante', NULL, 9, 4, 158, 3, 1200.00, 0.00, '00102', 98, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2013-11-05 20:30:27', '2013-11-06 10:07:03', '2013-11-22 00:00:00', '2013-11-06 10:03:08', 59, 59, NULL, '158', '2', 0, '59', 1, 0, 'HOT', '');

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_files`
--

CREATE TABLE IF NOT EXISTS `crms_lead_files` (
  `lead_files_name` text NOT NULL,
  `lead_files_created_by` int(4) NOT NULL,
  `lead_files_created_on` datetime NOT NULL,
  `lead_id` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_files`
--

INSERT INTO `crms_lead_files` (`lead_files_name`, `lead_files_created_by`, `lead_files_created_on`, `lead_id`) VALUES
('colors.txt', 170, '2013-07-19 19:10:56', 70),
('file-array.txt', 59, '2013-08-21 10:44:30', 78),
('junior-resources.xlsx', 59, '2013-08-21 10:49:25', 78),
('closed-opportunities-leads.xls', 59, '2013-08-21 10:49:27', 78),
('ecrm14-08.sql', 59, '2013-08-21 10:49:29', 78),
('closed-opportunities.txt', 59, '2013-08-21 10:49:32', 78),
('ecrm-tables-need-to-add-in-live.txt', 59, '2013-08-21 10:49:34', 78),
('ecrm-queries.txt', 59, '2013-08-21 10:49:40', 78),
('1377062382ecrm-queries.txt', 59, '2013-08-21 10:49:42', 78),
('phpchart.txt', 59, '2013-08-21 10:49:45', 78),
('colors.txt', 59, '2013-08-21 10:49:47', 78),
('tresu.doc', 59, '2013-08-21 10:49:54', 78),
('payngo.txt', 59, '2013-08-21 10:50:10', 78),
('file-array.txt', 59, '2013-08-26 19:45:25', 80),
('rupee-fall.jpg', 59, '2013-08-29 11:16:40', 90),
('lead-stage.txt', 59, '2013-09-02 16:40:12', 89),
('Document-Write-icon.png', 59, '2013-09-12 14:58:48', 91),
('electra.png', 59, '2013-09-23 11:53:09', 93),
('123456cat.jpg', 59, '2013-10-28 20:36:03', 95),
('temp.txt', 59, '2013-10-24 14:40:26', 93),
('config.html', 59, '2013-10-22 19:11:29', 89),
('tresu.doc', 59, '2013-10-22 19:12:17', 89),
('v.jpg', 59, '2013-10-29 15:27:36', 98),
('april.jpg', 59, '2013-10-30 14:42:15', 98),
('123456cat.jpg', 59, '2013-10-30 19:05:38', 98),
('images.jpg', 59, '2013-11-04 15:16:00', 101),
('1383561338images.jpg', 59, '2013-11-04 16:05:38', 101),
('images.jpg', 59, '2013-11-04 19:50:13', 54),
('imagesCACXM3R2.jpg', 59, '2013-11-04 19:50:56', 54),
('Lead-Dashboard.xls', 59, '2013-11-04 19:53:25', 101),
('Hydrangeas.jpg', 59, '2013-11-05 12:43:41', 101);

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_query`
--

CREATE TABLE IF NOT EXISTS `crms_lead_query` (
  `query_id` int(5) NOT NULL AUTO_INCREMENT,
  `lead_id` int(16) NOT NULL,
  `user_id` int(5) NOT NULL,
  `query_msg` varchar(1024) NOT NULL,
  `query_file_name` varchar(255) NOT NULL,
  `query_sent_date` datetime NOT NULL,
  `query_sent_to` varchar(255) NOT NULL,
  `query_from` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `replay_query` int(5) NOT NULL,
  PRIMARY KEY (`query_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `crms_lead_query`
--

INSERT INTO `crms_lead_query` (`query_id`, `lead_id`, `user_id`, `query_msg`, `query_file_name`, `query_sent_date`, `query_sent_to`, `query_from`, `status`, `replay_query`) VALUES
(1, 88, 59, 'dsfasdf', 'colors.txt', '2013-09-04 14:56:32', 'giri@gr.com', 'admin@enoahisolution.com', 'query', 0),
(2, 88, 59, 'saddf asdf', 'phpchart.txt', '2013-09-04 14:57:35', 'giri@gr.com', 'admin@enoahisolution.com', 'replay', 1),
(3, 93, 59, 'sdfdf', 'books.jpg', '2013-09-17 17:57:47', 'sswami@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(4, 93, 59, 'asdf', 'books1.jpg', '2013-09-17 18:00:58', 'sswami@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(5, 89, 59, 'dsfasdfasdf', 'sriram.jpg', '2013-10-22 19:12:41', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(6, 89, 59, 'asdfas%20dfasd%20fa', 'temp.txt', '2013-10-22 19:13:00', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 5),
(8, 93, 59, 'dsfasdf', 'april.jpg', '2013-10-25 10:53:55', 'sswami@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(9, 93, 59, 'asdf%20asdf', 'images.jpg', '2013-10-25 10:54:03', 'sswami@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 8),
(10, 93, 59, 'asd%20fas%20df', 'imagesCACXM3R2.jpg', '2013-10-25 10:54:19', 'sswami@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 9),
(11, 98, 59, 'asd%20f%20asdf%20asdfasdf', 'File Not Attached', '2013-10-30 12:31:59', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(12, 98, 59, 'as%20dfa%20sdf%20asdf%20asdfasdf', 'File Not Attached', '2013-10-30 12:52:15', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(13, 98, 59, 'wq%20ad%20adf%20', 'File Not Attached', '2013-10-30 12:55:33', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 12),
(14, 98, 59, 'a%20sdf%20asdf%20asdf', 'File Not Attached', '2013-10-30 13:00:10', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 11),
(15, 98, 59, 'tew%20e%20asdf%20asdf%20asdf%20asdf', 'File Not Attached', '2013-10-30 14:24:42', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 12),
(16, 98, 59, 'test%20test%20test%20test', 'File Not Attached', '2013-10-30 14:25:11', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 12),
(17, 98, 59, 'df%20asdf%20asdf%20asdf', 'File Not Attached', '2013-10-30 14:36:07', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 16),
(18, 98, 59, 'dsf%20asdf%20asdf%20asdf', 'File Not Attached', '2013-10-30 15:02:55', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(19, 98, 59, 'tewe%20rqwer%20adf%20asdf', 'v.jpg', '2013-10-30 15:07:21', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(20, 98, 59, 'df%20asdf%20asdf%20asdf', 'File Not Attached', '2013-10-30 15:18:55', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 12),
(21, 98, 59, 'adf%20asdf%20asf%20asdf%20asdf%20%20adsf%20asdf%20asdf', 'KK.bmp', '2013-10-30 15:19:41', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 12),
(22, 98, 59, 'as%20dfas%20dfa%20sdf', 'File Not Attached', '2013-10-30 15:22:32', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 21),
(23, 98, 59, 'eeer%20qvwe%20rqwer%20qwer%20qwer%20qwer%20qwer%20qwer', 'File Not Attached', '2013-10-30 15:23:42', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(24, 98, 59, 'submit%20submit%20subint', 'File Not Attached', '2013-10-30 15:25:01', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 23),
(25, 98, 59, 'last%20last%20last', 'File Not Attached', '2013-10-30 15:26:36', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(26, 98, 59, 'te%20ewr%20qwer%20qwer%20adf%20', 'images.jpg', '2013-10-30 15:26:54', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(27, 98, 59, 'ef%20asdf%20asdf%20asdf%20asdf%20asdf%20asdf%20asdf', 'g.bmp', '2013-10-30 15:27:20', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'query', 0),
(28, 98, 59, 'd%20asd%20asdf%20asdf', 'File Not Attached', '2013-10-30 15:27:37', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 25),
(29, 98, 59, 'f%20as%20fasd%20fasdf', 'File Not Attached', '2013-10-30 15:29:55', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 28),
(30, 98, 59, 'a%20sdf%20asdf', 'File Not Attached', '2013-10-30 15:30:02', 'ssriram@enoahisolution.com', 'admin@enoahisolution.com', 'replay', 28);

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
  `lead_id` int(11) NOT NULL,
  `dateofchange` datetime NOT NULL,
  `previous_status` int(11) NOT NULL,
  `changed_status` int(11) NOT NULL,
  `lead_status` int(1) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_stage_history`
--

INSERT INTO `crms_lead_stage_history` (`lead_id`, `dateofchange`, `previous_status`, `changed_status`, `lead_status`, `modified_by`) VALUES
(76, '2013-08-06 15:54:44', 6, 7, 1, 59),
(76, '2013-08-07 19:15:55', 7, 8, 1, 59),
(76, '2013-07-07 19:16:15', 8, 9, 1, 59),
(72, '2013-08-07 19:17:20', 6, 9, 1, 59),
(70, '2013-08-07 19:24:01', 2, 5, 4, 59),
(70, '2013-06-07 19:24:30', 5, 10, 4, 59),
(65, '2013-08-07 19:25:14', 3, 5, 1, 59),
(65, '2013-08-07 19:25:28', 5, 7, 1, 59),
(65, '2013-08-03 19:25:42', 7, 11, 1, 59),
(49, '2013-08-07 19:52:29', 8, 7, 1, 59),
(49, '2013-05-07 19:52:42', 7, 12, 1, 59),
(74, '2013-08-07 19:53:24', 1, 3, 1, 59),
(74, '2013-08-07 19:53:35', 3, 5, 1, 59),
(74, '2013-08-07 19:54:33', 5, 7, 1, 59),
(74, '2013-07-07 19:54:56', 7, 9, 1, 59),
(76, '2013-08-07 20:28:23', 9, 10, 1, 59),
(78, '2013-08-07 20:50:00', 1, 1, 4, 59),
(76, '2013-08-08 11:57:41', 8, 11, 1, 59),
(64, '2013-06-11 14:50:01', 9, 10, 1, 59),
(67, '2013-04-27 12:49:02', 1, 13, 1, 59),
(79, '2013-08-14 15:01:55', 1, 1, 1, 59),
(63, '2013-06-27 17:48:23', 4, 13, 1, 59),
(58, '2013-04-18 20:00:19', 1, 13, 1, 135),
(79, '2013-08-14 19:53:29', 1, 13, 1, 59),
(73, '2013-08-16 12:40:29', 1, 3, 1, 59),
(73, '2013-08-16 16:45:53', 3, 13, 1, 59),
(77, '2013-08-21 12:15:52', 1, 2, 1, 59),
(77, '2013-08-21 12:22:22', 2, 3, 1, 59),
(73, '2013-08-21 12:35:04', 5, 6, 1, 59),
(73, '2013-08-21 12:35:25', 6, 7, 1, 59),
(71, '2013-08-21 12:36:20', 1, 2, 1, 59),
(71, '2013-08-21 12:37:11', 2, 3, 1, 59),
(71, '2013-08-21 12:41:58', 3, 4, 1, 59),
(71, '2013-08-21 12:43:55', 4, 7, 1, 59),
(71, '2013-08-21 14:02:54', 7, 9, 1, 59),
(76, '2013-08-21 14:41:32', 11, 12, 1, 59),
(77, '2013-08-21 16:02:10', 3, 7, 1, 59),
(70, '2013-08-26 17:59:39', 10, 11, 4, 59),
(80, '2013-08-26 18:03:13', 1, 1, 1, 59),
(81, '2013-08-27 15:24:20', 1, 1, 1, 59),
(82, '2013-08-27 15:26:20', 1, 1, 4, 59),
(83, '2013-08-27 15:27:57', 1, 1, 4, 59),
(84, '2013-08-27 15:37:47', 1, 1, 4, 59),
(85, '2013-08-27 15:49:43', 1, 1, 1, 59),
(86, '2013-08-27 15:57:57', 1, 1, 1, 59),
(87, '2013-08-27 15:58:48', 1, 1, 1, 59),
(88, '2013-08-27 16:17:08', 1, 1, 4, 59),
(89, '2013-08-28 15:13:57', 1, 1, 1, 59),
(90, '2013-08-29 11:12:44', 1, 1, 1, 59),
(90, '2013-08-29 11:34:01', 1, 7, 1, 59),
(90, '2013-08-29 11:34:32', 7, 7, 1, 59),
(90, '2013-08-29 11:40:09', 7, 13, 1, 59),
(89, '2013-09-04 11:39:30', 1, 2, 1, 59),
(88, '2013-09-04 17:26:14', 1, 2, 4, 59),
(87, '2013-09-04 17:28:09', 1, 13, 1, 59),
(76, '2013-09-05 17:42:08', 12, 13, 1, 59),
(80, '2013-09-05 17:42:37', 1, 4, 1, 59),
(80, '2013-09-05 17:42:47', 4, 8, 1, 59),
(80, '2013-09-05 17:42:56', 8, 9, 1, 59),
(80, '2013-09-05 17:43:06', 9, 11, 1, 59),
(80, '2013-09-05 17:43:27', 11, 11, 1, 59),
(80, '2013-09-05 17:43:54', 11, 13, 1, 59),
(82, '2013-09-05 18:07:36', 1, 5, 4, 59),
(82, '2013-09-05 18:07:47', 5, 7, 4, 59),
(82, '2013-09-05 18:07:57', 7, 11, 4, 59),
(82, '2013-09-05 18:08:24', 11, 12, 4, 59),
(89, '2013-09-11 14:39:18', 2, 3, 1, 59),
(53, '2013-09-11 17:35:30', 7, 6, 3, 59),
(72, '2013-09-11 19:01:33', 9, 13, 1, 59),
(91, '2013-09-12 14:57:37', 1, 1, 4, 59),
(92, '2013-09-16 12:32:16', 1, 1, 4, 59),
(93, '2013-09-16 13:39:47', 1, 1, 1, 158),
(93, '2013-09-16 17:54:59', 1, 12, 1, 59),
(93, '2013-09-16 17:55:21', 12, 12, 1, 59),
(93, '2013-09-16 19:01:17', 12, 13, 1, 59),
(92, '2013-09-16 19:06:50', 1, 2, 4, 59),
(92, '2013-09-16 19:09:25', 2, 3, 4, 59),
(92, '2013-09-16 19:15:56', 3, 4, 4, 59),
(92, '2013-09-16 19:18:18', 4, 5, 4, 59),
(92, '2013-09-16 19:20:15', 5, 7, 4, 59),
(92, '2013-09-16 19:23:02', 7, 6, 4, 59),
(92, '2013-09-16 19:23:18', 6, 13, 4, 59),
(94, '2013-09-16 19:25:51', 1, 1, 4, 173),
(94, '2013-09-16 19:26:27', 1, 2, 4, 173),
(94, '2013-09-16 19:26:40', 2, 5, 4, 173),
(94, '2013-09-16 19:26:56', 5, 13, 4, 173),
(93, '2013-09-18 11:03:31', 13, 12, 1, 59),
(84, '2013-09-20 15:28:41', 1, 3, 4, 59),
(95, '2013-09-20 15:34:19', 1, 1, 4, 59),
(95, '2013-09-20 15:35:47', 1, 2, 4, 59),
(95, '2013-09-20 15:36:35', 2, 3, 4, 59),
(95, '2013-09-20 15:37:06', 3, 13, 4, 59),
(96, '2013-09-24 15:49:00', 1, 1, 1, 59),
(96, '2013-10-21 13:00:53', 3, 4, 1, 59),
(96, '2013-10-21 13:46:59', 7, 5, 1, 59),
(96, '2013-10-21 13:49:27', 5, 6, 1, 59),
(96, '2013-10-21 13:50:01', 6, 9, 1, 59),
(97, '2013-10-22 15:55:02', 1, 1, 1, 59),
(97, '2013-10-22 17:05:28', 1, 2, 1, 59),
(97, '2013-10-22 17:10:41', 2, 3, 1, 59),
(97, '2013-10-22 17:10:53', 3, 4, 1, 59),
(97, '2013-10-22 17:25:24', 4, 7, 1, 59),
(97, '2013-10-22 17:30:21', 7, 5, 1, 59),
(97, '2013-10-22 17:32:10', 5, 6, 1, 59),
(97, '2013-10-22 17:33:16', 6, 9, 1, 59),
(89, '2013-10-22 17:35:22', 3, 4, 1, 59),
(89, '2013-10-22 17:36:45', 4, 7, 1, 59),
(93, '2013-10-23 18:47:54', 12, 13, 1, 59),
(98, '2013-10-29 13:47:58', 1, 1, 1, 59),
(99, '2013-10-29 13:50:01', 1, 1, 1, 59),
(100, '2013-10-29 16:21:57', 1, 1, 1, 59),
(98, '2013-10-29 20:59:21', 1, 2, 1, 59),
(101, '2013-10-31 18:13:51', 1, 1, 2, 59),
(101, '2013-10-31 18:15:38', 1, 2, 2, 59),
(101, '2013-10-31 18:22:24', 2, 4, 2, 59),
(101, '2013-10-31 18:25:32', 4, 3, 2, 59),
(101, '2013-10-31 18:25:52', 3, 7, 2, 59),
(101, '2013-10-31 18:26:28', 7, 5, 2, 59),
(85, '2013-10-31 18:29:33', 1, 2, 1, 59),
(85, '2013-10-31 18:30:05', 2, 4, 1, 59),
(101, '2013-11-04 16:44:24', 5, 6, 2, 59),
(102, '2013-11-05 20:30:27', 1, 1, 1, 59),
(103, '2013-11-05 20:33:56', 1, 1, 1, 59),
(102, '2013-11-05 21:19:36', 1, 2, 1, 59),
(102, '2013-11-06 10:07:27', 2, 4, 1, 59);

-- --------------------------------------------------------

--
-- Table structure for table `crms_lead_status_history`
--

CREATE TABLE IF NOT EXISTS `crms_lead_status_history` (
  `lead_id` int(11) NOT NULL,
  `dateofchange` datetime NOT NULL,
  `changed_status` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crms_lead_status_history`
--

INSERT INTO `crms_lead_status_history` (`lead_id`, `dateofchange`, `changed_status`, `modified_by`) VALUES
(91, '2013-09-12 14:57:37', 1, 59),
(91, '2013-09-12 15:19:48', 3, 59),
(91, '2013-09-12 15:18:27', 2, 59),
(91, '2013-09-12 15:20:13', 4, 59),
(67, '2013-04-27 16:27:08', 4, 59),
(79, '2013-08-14 19:53:29', 4, 59),
(87, '2013-09-04 17:28:09', 4, 59),
(87, '2013-09-04 17:28:09', 3, 59),
(80, '2013-09-05 17:43:54', 4, 59),
(76, '2013-08-13 17:42:08', 4, 59),
(53, '2013-06-11 18:16:48', 4, 59),
(82, '2013-01-16 18:19:07', 3, 59),
(82, '2013-06-16 18:19:31', 4, 59),
(92, '2013-09-16 12:32:16', 1, 59),
(93, '2013-09-16 13:39:47', 1, 158),
(92, '2013-09-16 19:23:29', 4, 59),
(94, '2013-09-16 19:25:51', 1, 173),
(94, '2013-09-16 19:27:36', 4, 173),
(84, '2013-09-20 15:29:02', 4, 59),
(95, '2013-09-20 15:34:19', 1, 59),
(95, '2013-09-20 15:37:17', 4, 59),
(73, '2013-09-23 15:29:27', 1, 59),
(96, '2013-09-24 15:49:00', 1, 59),
(97, '2013-10-22 15:55:02', 1, 59),
(83, '2013-10-23 15:50:59', 4, 59),
(78, '2013-10-25 15:16:07', 4, 59),
(98, '2013-10-29 13:47:58', 1, 59),
(99, '2013-10-29 13:50:01', 1, 59),
(100, '2013-10-29 16:21:57', 1, 59),
(50, '2013-10-31 16:54:31', 1, 59),
(101, '2013-10-31 18:13:51', 1, 59),
(101, '2013-10-31 18:15:31', 2, 59),
(53, '2013-11-04 16:47:08', 3, 59),
(102, '2013-11-05 20:30:27', 1, 59),
(103, '2013-11-05 20:33:56', 1, 59),
(70, '2013-11-05 21:38:43', 4, 59);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=217 ;

--
-- Dumping data for table `crms_levels_country`
--

INSERT INTO `crms_levels_country` (`levels_country_id`, `level_id`, `country_id`, `user_id`) VALUES
(198, 5, 18, 173),
(214, 5, 17, 157),
(186, 4, 50, 169),
(182, 3, 18, 168),
(216, 3, 23, 147),
(197, 4, 18, 172),
(200, 4, 18, 174),
(210, 5, 15, 149),
(202, 5, 18, 152),
(207, 4, 30, 154),
(212, 5, 50, 164),
(193, 3, 50, 160);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=217 ;

--
-- Dumping data for table `crms_levels_location`
--

INSERT INTO `crms_levels_location` (`levels_location_id`, `level_id`, `location_id`, `user_id`) VALUES
(197, 5, 75, 173),
(216, 5, 1, 157),
(210, 5, 9, 149),
(209, 5, 8, 149),
(199, 5, 61, 152),
(214, 5, 60, 164),
(208, 5, 3, 149),
(211, 5, 45, 149),
(212, 5, 56, 149);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=226 ;

--
-- Dumping data for table `crms_levels_region`
--

INSERT INTO `crms_levels_region` (`levels_region_id`, `level_id`, `region_id`, `user_id`) VALUES
(191, 4, 1, 174),
(186, 5, 1, 173),
(185, 4, 1, 172),
(160, 2, 46, 139),
(211, 5, 1, 157),
(132, 2, 45, 166),
(178, 3, 46, 160),
(225, 3, 3, 147),
(189, 2, 3, 161),
(203, 5, 1, 149),
(194, 5, 1, 152),
(117, 2, 3, 150),
(199, 4, 8, 154),
(209, 5, 46, 164),
(169, 2, 46, 167),
(168, 2, 27, 167),
(167, 2, 8, 167),
(166, 2, 2, 167),
(159, 2, 27, 139),
(158, 2, 8, 139),
(157, 2, 2, 139),
(146, 3, 1, 168),
(171, 2, 1, 155),
(149, 4, 46, 169),
(223, 2, 1, 158),
(172, 2, 2, 155),
(222, 2, 2, 171),
(221, 2, 1, 171),
(208, 2, 1, 189);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1027 ;

--
-- Dumping data for table `crms_levels_state`
--

INSERT INTO `crms_levels_state` (`levels_state_id`, `level_id`, `state_id`, `user_id`) VALUES
(1026, 5, 113, 157),
(960, 4, 152, 169),
(977, 4, 47, 172),
(981, 4, 47, 174),
(1022, 5, 24, 149),
(984, 5, 153, 152),
(1019, 4, 136, 154),
(1018, 4, 135, 154),
(1017, 4, 134, 154),
(1016, 4, 132, 154),
(1015, 4, 131, 154),
(1014, 4, 130, 154),
(1013, 4, 129, 154),
(1012, 4, 128, 154),
(1011, 4, 127, 154),
(1010, 4, 126, 154),
(1009, 4, 125, 154),
(1024, 5, 152, 164),
(978, 5, 47, 173),
(982, 4, 50, 174);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `crms_location`
--

INSERT INTO `crms_location` (`locationid`, `location_name`, `stateid`, `created_by`, `modified_by`, `created`, `modified`, `inactive`) VALUES
(1, 'Singapore City', 113, 59, 59, '2013-01-22 19:14:52', '2013-01-22 19:14:52', 0),
(3, 'Chennai', 24, 59, 59, '2013-01-23 21:12:03', '2013-01-23 21:12:03', 0),
(4, 'Bangalore', 12, 59, 59, '2013-01-23 21:12:17', '2013-11-19 15:47:36', 1),
(5, 'Mumbai', 15, 59, 59, '2013-01-23 21:12:58', '2013-01-23 21:12:58', 0),
(6, 'New Delhi', 33, 59, 59, '2013-01-23 21:13:25', '2013-01-23 21:13:25', 0),
(7, 'Hyderabad', 1, 59, 59, '2013-01-23 21:13:38', '2013-01-23 21:13:38', 0),
(8, 'Madurai', 24, 59, 59, '2013-01-23 21:13:52', '2013-01-23 21:13:52', 0),
(9, 'Coimbatore', 24, 59, 59, '2013-01-23 21:14:05', '2013-01-23 21:14:05', 0),
(10, 'Kuala Lampur', 114, 59, 59, '2013-01-23 21:30:47', '2013-01-23 21:30:47', 0),
(11, 'Kolkatta', 28, 59, 59, '2013-01-23 21:31:04', '2013-01-23 21:31:04', 0),
(12, 'Sydney', 44, 59, 59, '2013-01-23 21:31:20', '2013-01-23 21:31:20', 0),
(13, 'Panama City', 24, 59, 59, '2013-01-29 15:25:36', '2013-01-29 15:25:36', 0),
(27, 'Katagami', 125, 59, 59, '2013-02-01 09:55:32', '2013-02-01 09:55:32', 0),
(28, 'Oga', 125, 59, 59, '2013-02-01 09:55:43', '2013-02-01 09:55:43', 0),
(29, 'Hachinohe', 126, 59, 59, '2013-02-01 09:55:58', '2013-02-01 09:55:58', 0),
(30, 'Mutsu', 126, 59, 59, '2013-02-01 09:56:11', '2013-02-01 09:56:11', 0),
(25, 'Noshiro', 125, 59, 59, '2013-02-01 09:47:03', '2013-02-01 09:47:03', 0),
(26, 'Daisen', 125, 59, 59, '2013-02-01 09:55:19', '2013-02-01 09:55:19', 0),
(32, 'Narita', 127, 59, 59, '2013-02-01 09:56:58', '2013-02-01 09:56:58', 0),
(31, 'Tsugaru', 126, 59, 59, '2013-02-01 09:56:24', '2013-02-01 09:56:24', 0),
(33, 'Kashiwa', 127, 59, 59, '2013-02-01 09:57:13', '2013-02-01 09:57:13', 0),
(34, 'Yawatahama', 128, 59, 59, '2013-02-01 09:57:30', '2013-02-01 09:57:30', 0),
(35, 'Antartica Locat', 137, 59, 59, '2013-02-05 11:34:24', '2013-11-19 15:40:13', 0),
(36, 'test location', 138, 59, 59, '2013-02-05 15:44:41', '2013-02-05 15:44:41', 1),
(37, 'test location', 139, 59, 59, '2013-02-14 16:30:55', '2013-02-14 16:30:55', 1),
(38, 'karachi', 140, 59, 59, '2013-02-14 16:32:58', '2013-02-14 16:32:58', 0),
(39, 'karachi1', 141, 59, 59, '2013-02-14 16:32:58', '2013-02-14 16:32:58', 0),
(40, 'test l', 142, 59, 59, '2013-02-14 17:40:19', '2013-11-19 12:10:28', 1),
(44, 'South Location', 146, 150, 150, '2013-02-15 20:47:46', '2013-02-15 20:47:46', 0),
(42, 'Chennai', 144, 59, 59, '2013-02-15 17:08:28', '2013-02-15 17:08:28', 0),
(43, 'Location 65', 145, 59, 59, '2013-02-15 17:13:05', '2013-02-15 17:13:05', 0),
(45, 'Tirunelveli', 24, 59, 59, '2013-02-19 15:30:15', '2013-02-19 15:30:15', 0),
(46, 'Africa2', 149, 59, 59, '2013-02-20 11:47:17', '2013-02-20 11:47:17', 1),
(48, 'Las Vegas', 53, 59, 59, '2013-02-20 11:55:05', '2013-02-20 11:55:05', 0),
(49, 'Africa', 150, 59, 59, '2013-02-20 15:57:53', '2013-11-19 15:46:48', 1),
(75, 'Queensland loc1', 47, 59, 59, '2013-07-29 19:33:22', '2013-07-29 19:33:22', 0),
(74, 'Queensland loc', 47, 59, 59, '2013-07-29 19:25:22', '2013-07-29 19:25:22', 0),
(54, 'test ss', 142, 59, 59, '2013-02-22 20:33:07', '2013-11-19 12:10:50', 1),
(55, 'tesetl', 142, 59, 59, '2013-02-22 21:15:34', '2013-11-19 12:10:05', 1),
(56, 'Tuticorin', 24, 59, 59, '2013-02-26 19:56:09', '2013-02-26 19:56:09', 0),
(57, 'test loi', 142, 59, 59, '2013-03-06 17:57:30', '2013-11-19 12:10:38', 1),
(58, 'south america location', 151, 59, 59, '2013-03-13 16:37:56', '2013-03-13 16:37:56', 0),
(59, 'Iwate loc', 136, 59, 59, '2013-03-15 10:48:19', '2013-03-15 10:48:19', 0),
(60, 'Mongolia', 152, 59, 59, '2013-03-27 15:22:53', '2013-03-27 15:22:53', 0),
(61, 'Tripura', 153, 161, 161, '2013-03-29 12:59:19', '2013-03-29 12:59:19', 0),
(62, 'Trichy', 24, 161, 161, '2013-03-29 17:31:01', '2013-03-29 17:31:01', 0),
(63, 'Cochin', 13, 59, 59, '2013-03-29 17:42:08', '2013-03-29 17:42:08', 0),
(64, 'France loc', 154, 139, 139, '2013-04-24 14:44:05', '2013-04-24 14:44:05', 0),
(65, 'Victoria loc', 50, 155, 155, '2013-04-26 11:06:51', '2013-04-26 11:06:51', 0),
(69, 'Victoria loc1', 50, 161, 59, '2013-04-30 11:59:03', '2013-11-19 19:28:23', 0),
(70, 'Harare Locn', 156, 139, 139, '2013-05-06 16:52:36', '2013-05-06 16:52:36', 0),
(71, 'Kadoma', 157, 139, 139, '2013-05-06 17:02:28', '2013-05-06 17:02:28', 0),
(72, 'Berlin Locn', 158, 139, 139, '2013-05-06 17:16:55', '2013-05-06 17:16:55', 0),
(73, 'Dublin locn', 159, 139, 139, '2013-05-06 18:14:20', '2013-05-06 18:14:20', 0),
(76, 'SpringField', 65, 59, 59, '2013-09-24 15:48:21', '2013-11-05 10:21:23', 0),
(77, 'Bihar Loc', 4, 59, 59, '2013-11-19 11:43:50', '2013-11-19 11:43:50', 0),
(78, 'loc1-1-1', 161, 59, 59, '2013-11-19 16:49:22', '2013-11-19 18:04:59', 0),
(79, 'loc1-2-1', 162, 59, 59, '2013-11-19 18:05:18', '2013-11-19 18:05:18', 0),
(80, 'loc1-2-2', 162, 59, 59, '2013-11-19 18:05:47', '2013-11-19 18:05:47', 0),
(81, 'loc2-2-1', 164, 59, 59, '2013-11-19 18:07:02', '2013-11-19 18:07:02', 0),
(82, 'loc3-1-1', 165, 59, 59, '2013-11-19 18:07:26', '2013-11-19 18:07:26', 0),
(83, 'loc3-2-2', 166, 59, 59, '2013-11-19 18:07:45', '2013-11-19 18:07:45', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1106 ;

--
-- Dumping data for table `crms_logs`
--

INSERT INTO `crms_logs` (`logid`, `jobid_fk`, `userid_fk`, `date_created`, `log_content`, `stickie`, `time_spent`, `attached_docs`) VALUES
(152, 45, 59, '2013-03-29 11:26:11', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00045 ', 0, NULL, NULL),
(14, 2, 118, '2013-01-24 00:11:14', 'Status Changed to: Demo Scheduled Sucessfully on Lead No.00002 ', 0, NULL, NULL),
(15, 2, 118, '2013-01-24 00:12:41', 'Provided a demo on our moodle learning portal solution to the client and Manoj.  Client has to come back with his exact requirements', 0, NULL, NULL),
(151, 46, 160, '2013-03-27 16:49:17', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00046 ', 0, NULL, NULL),
(546, 53, 59, '2013-07-15 17:56:47', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - HooperHolmes ', 0, NULL, NULL),
(58, 8, 145, '2013-01-29 18:12:19', 'Actual Worth Amount Modified On : Jan 29, 2013 6:12 PM', 0, NULL, NULL),
(50, 1, 59, '2013-01-29 17:21:56', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(49, 1, 59, '2013-01-29 17:21:41', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(48, 1, 59, '2013-01-29 17:21:39', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(545, 67, 59, '2013-07-15 17:37:07', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(24, 3, 122, '2013-01-25 06:18:20', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00003 ', 0, NULL, NULL),
(25, 4, 122, '2013-01-25 06:40:43', 'Lead from Partner Electra.\n\nClient is an Australian company that had already outsourced ecommerce portal development to an IT company in Singapore, Proj dev just started and expected to complete in June.\n\nElectra was positioning B1.  I demoed our shopping cart, customer impressed and considering switching to us.\n\nPlan is to provide eShopping Cart and later integrate with SAP B1.\nHave provided electra with standard rate card and a dealer discount of 15%.  ', 0, NULL, NULL),
(27, 4, 122, '2013-01-25 06:44:32', 'Status Changed to: Proposal WIP Sucessfully on Lead No.00004 ', 0, NULL, NULL),
(544, 67, 59, '2013-07-15 17:36:15', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(52, 1, 59, '2013-01-29 17:27:01', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(55, 7, 59, '2013-01-29 17:29:09', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(56, 8, 145, '2013-01-29 18:03:14', 'Status Changed to: POC in Progress Sucessfully on Lead No.00008 ', 0, NULL, NULL),
(543, 67, 59, '2013-07-15 17:35:51', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(542, 67, 59, '2013-07-15 17:35:19', 'Status Changed to: SOW under Review Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(35, 4, 118, '2013-01-28 07:17:52', 'Actual Worth Amount Modified On : Jan 28, 2013 7:17 AM', 0, NULL, NULL),
(36, 4, 118, '2013-01-28 07:18:02', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00004 ', 0, NULL, NULL),
(37, 4, 118, '2013-01-28 07:19:10', 'MUMS proposal shared thru mail and uploaded within this CRM system.  All files uploaded here.<br /><br />This log has been emailed to:<br />Manoj Sherman, Mukesh Vaidyanathan', 0, NULL, NULL),
(541, 67, 59, '2013-07-15 17:35:00', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(40, 4, 118, '2013-01-28 11:22:38', 'Actual Worth Amount Modified On : Jan 28, 2013 11:22 AM', 0, NULL, NULL),
(540, 67, 59, '2013-07-15 17:34:38', 'Status Changed to: Declined Proposals Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(42, 4, 118, '2013-01-28 11:25:22', 'Uploaded the auction and referral module requirements sent in mail by manoj into this system.', 0, NULL, NULL),
(59, 8, 145, '2013-01-29 18:12:19', 'Lead has been reassigned to:&nbsp;Vijay&nbsp;S<br/>For Lead No.00008 ', 0, NULL, NULL),
(539, 67, 59, '2013-07-15 17:34:17', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(47, 1, 59, '2013-01-29 17:21:36', 'Lead Deleted Sucessfully - Lead No. ', 0, NULL, NULL),
(538, 67, 59, '2013-07-15 17:33:40', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(148, 46, 160, '2013-03-27 16:22:34', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00046 ', 0, NULL, NULL),
(67, 15, 145, '2013-01-31 17:31:18', 'Lead has been reassigned to:&nbsp;Vijay&nbsp;S<br/>For Lead No.00015 ', 0, NULL, NULL),
(537, 67, 59, '2013-07-15 17:33:13', 'Status Changed to: Proposal WIP Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(143, 44, 59, '2013-03-25 20:01:58', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(536, 67, 59, '2013-07-15 17:32:41', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(535, 67, 59, '2013-07-15 17:32:20', 'Status Changed to: POC in Progress Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(142, 44, 59, '2013-03-25 20:01:45', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(77, 26, 139, '2013-02-14 16:52:27', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', 0, NULL, NULL),
(78, 26, 139, '2013-02-14 16:52:53', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem..<br /><br />This log has been emailed to:<br />Ramji BH', 0, NULL, NULL),
(137, 43, 59, '2013-03-21 12:34:56', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(138, 33, 59, '2013-03-25 19:07:57', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00033 ', 0, NULL, NULL),
(139, 44, 139, '2013-03-25 19:37:49', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00044 ', 0, NULL, NULL),
(140, 44, 139, '2013-03-25 19:41:06', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(141, 44, 139, '2013-03-25 19:58:54', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(81, 33, 59, '2013-02-19 13:20:17', 'Actual Worth Amount Modified On : Feb 19, 2013 1:20 PM', 0, NULL, NULL),
(82, 33, 59, '2013-02-19 14:46:49', 'Status Changed to: Declined Proposals Sucessfully on Lead No.00033 ', 0, NULL, NULL),
(83, 34, 59, '2013-02-20 14:32:56', 'Actual Worth Amount Modified On : Feb 20, 2013 2:32 PM', 0, NULL, NULL),
(84, 34, 59, '2013-02-20 14:38:53', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(85, 34, 59, '2013-02-20 14:39:17', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(86, 34, 59, '2013-02-20 14:39:44', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(87, 34, 59, '2013-02-20 14:41:35', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(88, 34, 59, '2013-02-20 14:51:14', 'Status Changed to: Demo Scheduled Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(89, 34, 59, '2013-02-20 14:51:55', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(90, 34, 59, '2013-02-20 14:53:01', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(91, 34, 59, '2013-02-20 14:55:30', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(92, 34, 59, '2013-02-20 15:12:17', 'Status Changed to: Proposal WIP Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(534, 67, 59, '2013-07-15 17:31:20', 'Status Changed to: Prospect Sucessfully for the Lead - surya lead 4 test ', 0, NULL, NULL),
(94, 34, 59, '2013-02-20 15:19:54', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(95, 34, 59, '2013-02-20 15:20:34', 'Lead has been reassigned to:&nbsp;Admin&nbsp;eNoah - iSolution<br/>For Lead No.00034 ', 0, NULL, NULL),
(96, 34, 59, '2013-02-20 15:20:53', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully on Lead No.00034 ', 0, NULL, NULL),
(97, 34, 59, '2013-02-20 15:21:29', 'Lead has been reassigned to:&nbsp;Vijay&nbsp;S<br/>For Lead No.00034 ', 0, NULL, NULL),
(99, 35, 148, '2013-02-20 16:18:46', 'Status Changed to: SOW under Review Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(100, 35, 148, '2013-02-20 16:19:16', 'Status Changed to: SOW under Review Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(101, 35, 148, '2013-02-20 16:19:56', 'Status Changed to: SOW under Review Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(102, 35, 148, '2013-02-20 16:20:21', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(103, 35, 59, '2013-02-21 15:04:14', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(104, 35, 59, '2013-02-21 15:05:25', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00035 ', 0, NULL, NULL),
(108, 43, 59, '2013-03-19 19:14:35', '\nTimeline for the project: Suraj Test project\n26-03-2013 : Lorem Ipsum is simply dummy text of the printing and typesetting industry\n', 0, NULL, NULL),
(109, 43, 59, '2013-03-19 19:15:27', 'Status Changed to: SOW under Review Sucessfully on Lead No.00043 ', 0, NULL, NULL),
(110, 43, 59, '2013-03-19 19:15:38', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00043 ', 0, NULL, NULL),
(111, 43, 59, '2013-03-19 19:15:53', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00043 ', 0, NULL, NULL),
(112, 43, 59, '2013-03-19 19:17:49', '\nTimeline for the project: Suraj Test project\n26-03-2013 : Lorem Ipsum is simply dummy text of the printing and typesetting industry\n26-03-2013 : Lorem Ipsum is simply dummy\n', 0, NULL, NULL),
(113, 41, 59, '2013-03-19 19:21:59', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00041 ', 0, NULL, NULL),
(114, 26, 59, '2013-03-19 19:22:19', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00026 ', 0, NULL, NULL),
(115, 25, 59, '2013-03-19 19:22:32', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00025 ', 0, NULL, NULL),
(116, 36, 59, '2013-03-19 19:30:11', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00036 ', 0, NULL, NULL),
(117, 36, 59, '2013-03-19 19:30:41', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(118, 25, 156, '2013-03-19 20:28:20', '\nTimeline for the project: test\n26-03-2013 : Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00025 \n', 0, NULL, NULL),
(533, 61, 59, '2013-07-15 17:19:19', 'Invoice No: p1  Amount: SGD 1000  Deposit Date: 2013-07-14 Map term:21 is created.', 0, NULL, 'Invoice No: p1  Amount: SGD 1000  Deposit Date: 2013-07-14 Map term:21'),
(136, 43, 59, '2013-03-21 11:51:31', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(153, 45, 59, '2013-03-29 11:31:18', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00045 ', 0, NULL, NULL),
(154, 38, 59, '2013-03-29 11:49:03', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00038 ', 0, NULL, NULL),
(155, 37, 59, '2013-03-29 11:50:55', 'Status Changed to: Demo Scheduled Sucessfully on Lead No.00037 ', 0, NULL, NULL),
(156, 25, 59, '2013-03-29 11:58:06', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(157, 25, 59, '2013-03-29 11:58:22', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(158, 36, 59, '2013-03-29 11:59:10', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(159, 37, 59, '2013-03-29 12:21:34', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00037 ', 0, NULL, NULL),
(160, 48, 161, '2013-03-29 14:18:31', 'Status Changed to: POC in Progress Sucessfully on Lead No.00048 ', 0, NULL, NULL),
(161, 48, 161, '2013-03-29 14:31:49', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00048 ', 0, NULL, NULL),
(162, 38, 59, '2013-04-01 11:08:57', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(163, 38, 59, '2013-04-01 11:10:04', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(164, 50, 59, '2013-04-01 11:11:39', 'Any Comments ', 1, 1, NULL),
(165, 50, 59, '2013-04-01 11:11:58', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(166, 48, 161, '2013-04-01 11:19:40', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(168, 48, 161, '2013-04-01 11:23:04', '\nTimeline for the project: IT Project\n16-04-2013 : HH\n<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(169, 48, 161, '2013-04-01 11:33:52', '\nTimeline for the project: IT Project\n17-04-2013 : HH\n23-04-2013 : HH\n<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(170, 48, 161, '2013-04-01 11:36:08', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(171, 48, 161, '2013-04-01 11:40:08', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(172, 51, 158, '2013-04-01 11:46:39', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully on Lead No.00051 ', 0, NULL, NULL),
(173, 51, 158, '2013-04-01 11:46:47', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00051 ', 0, NULL, NULL),
(174, 51, 158, '2013-04-01 11:47:56', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(175, 48, 161, '2013-04-01 12:11:00', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(176, 48, 161, '2013-04-01 12:11:04', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(177, 48, 161, '2013-04-01 12:11:11', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(178, 48, 161, '2013-04-01 12:25:50', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(180, 48, 161, '2013-04-01 14:48:19', '\nTimeline for the project: IT Project\n17-04-2013 : HH\n', 0, NULL, NULL),
(181, 48, 161, '2013-04-01 14:48:40', '\nTimeline for the project: IT Project\n17-04-2013 : HH\n23-04-2013 : HH\n', 0, NULL, NULL),
(182, 48, 161, '2013-04-01 14:50:13', '\nTimeline for the project: IT Project\n17-04-2013 : HH\n23-04-2013 : HH\n<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(532, 61, 59, '2013-07-15 17:18:58', 'Project Milestone Name: p1  Amount: SGD 1500  Expected Date: 2013-07-01 is created.', 0, NULL, 'Project Milestone Name: p1  Amount: SGD 1500  Expected Date: 2013-07-01'),
(531, 66, 59, '2013-07-11 17:03:54', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - lead frm dinesh ', 0, NULL, NULL),
(703, 56, 59, '2013-09-12 14:12:11', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(188, 53, 161, '2013-04-02 12:16:57', 'CRM-Test-Results-Summary.doc is added.', 0, NULL, 'CRM-Test-Results-Summary.doc'),
(189, 53, 161, '2013-04-02 12:16:59', 'CRM-Test-Results-Summary-Final.doc is added.', 0, NULL, 'CRM-Test-Results-Summary-Final.doc'),
(190, 53, 161, '2013-04-02 12:17:36', '\nTimeline for the project: HooperHolmes\n : \n<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(191, 53, 59, '2013-04-02 12:40:48', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(476, 37, 59, '2013-05-10 18:20:11', 'Project Milestone Name: Payment No 1  Amount: USD 1500  Expected Date: 2013-04-01 is created.', 0, NULL, 'Project Milestone Name: Payment No 1  Amount: USD 1500  Expected Date: 2013-04-01'),
(193, 49, 59, '2013-04-02 16:00:00', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(194, 53, 161, '2013-04-02 16:02:41', 'Status Changed to: Declined Proposals Sucessfully on Lead No.00053 ', 0, NULL, NULL),
(195, 44, 59, '2013-04-02 17:52:47', 'Sample Email<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(196, 44, 59, '2013-04-02 17:53:38', 'Thanks\n<br /><br />This log has been emailed to:<br />Vijay Venkat', 0, NULL, NULL),
(197, 48, 59, '2013-04-02 19:05:12', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(198, 45, 59, '2013-04-16 14:28:37', 'Project Milestone Name: test payment1  Amount: AUD 1000  Expected Date: 2013-04-16 is created.', 0, NULL, 'Project Milestone Name: test payment1  Amount: AUD 1000  Expected Date: 2013-04-16'),
(199, 45, 59, '2013-04-16 14:28:53', 'Project Milestone Name: test payment2  Amount: AUD 1000  Expected Date: 2013-04-18 is created.', 0, NULL, 'Project Milestone Name: test payment2  Amount: AUD 1000  Expected Date: 2013-04-18'),
(200, 45, 59, '2013-04-16 14:29:30', 'Invoice No: Inv009  Amount: AUD 1000  Deposit Date: 2013-04-17 Map term:6 is created.', 0, NULL, 'Invoice No: Inv009  Amount: AUD 1000  Deposit Date: 2013-04-17 Map term:6'),
(201, 45, 59, '2013-04-16 14:30:01', 'Project Milestone Name: test payment3  Amount: AUD 500  Expected Date: 2013-04-20 is created.', 0, NULL, 'Project Milestone Name: test payment3  Amount: AUD 500  Expected Date: 2013-04-20'),
(202, 45, 59, '2013-04-16 14:30:30', 'Invoice No: Inv010  Amount: AUD 500  Deposit Date: 2013-04-19 Map term:7 is created.', 0, NULL, 'Invoice No: Inv010  Amount: AUD 500  Deposit Date: 2013-04-19 Map term:7'),
(203, 47, 59, '2013-04-16 18:28:35', 'Lead Onhold Reason: test reason for onhold', 0, NULL, NULL),
(204, 47, 59, '2013-04-16 18:31:01', 'Lead Onhold Reason: payment delayed', 0, NULL, NULL),
(205, 50, 59, '2013-04-16 18:35:47', 'Lead Onhold Reason: payment delayed', 0, NULL, NULL),
(206, 50, 59, '2013-04-16 18:35:47', 'Actual Worth Amount Modified On : Apr 16, 2013 6:36 PM', 0, NULL, NULL),
(207, 49, 59, '2013-04-16 18:58:43', 'Lead Onhold Reason: any reason for onhold', 0, NULL, NULL),
(208, 49, 59, '2013-04-16 18:58:43', 'Actual Worth Amount Modified On : Apr 16, 2013 6:59 PM', 0, NULL, NULL),
(209, 47, 59, '2013-04-17 10:49:34', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00047 ', 0, NULL, NULL),
(210, 47, 59, '2013-04-17 10:50:25', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00047 ', 0, NULL, NULL),
(211, 55, 165, '2013-04-17 13:14:34', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00055 ', 0, NULL, NULL),
(212, 55, 165, '2013-04-17 13:14:44', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00055 ', 0, NULL, NULL),
(213, 55, 165, '2013-04-17 13:14:56', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00055 ', 0, NULL, NULL),
(214, 56, 59, '2013-04-17 17:39:08', 'tatest<br /><br />This log has been emailed to:<br />Sriram S', 0, 15, NULL),
(215, 53, 59, '2013-04-18 11:19:08', 'tresu.doc is added.', 0, NULL, 'tresu.doc'),
(216, 45, 139, '2013-04-18 11:36:04', 'tresu.doc is added.', 0, NULL, 'tresu.doc'),
(217, 45, 139, '2013-04-18 11:40:30', 'level-concept-ecrm.sql is added.', 0, NULL, 'level-concept-ecrm.sql'),
(218, 45, 139, '2013-04-18 12:14:42', 'payments.txt is added.', 0, NULL, 'payments.txt'),
(219, 45, 139, '2013-04-18 12:16:11', 'ecrmlive-22server-1 is added.', 0, NULL, 'ecrmlive-22server-1'),
(220, 56, 59, '2013-04-18 12:51:06', 'test for time-spent', 0, 10, NULL),
(221, 56, 59, '2013-04-18 15:05:09', 'faetweer', 0, 1500, NULL),
(222, 56, 59, '2013-04-18 15:14:35', 'test ramji<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(223, 33, 59, '2013-04-18 17:50:25', 'Project Milestone Name: Payment #1  Amount: AUD 1000  Expected Date: 2013-04-19 is created.', 0, NULL, 'Project Milestone Name: Payment #1  Amount: AUD 1000  Expected Date: 2013-04-19'),
(224, 33, 59, '2013-04-18 17:50:40', 'Project Milestone Name: Payment #1  Amount: AUD 1000.00  Expected Date: 2013-04-19 is updated.', 0, NULL, 'Project Milestone Name: Payment #1  Amount: AUD 1000.00  Expected Date: 2013-04-19'),
(225, 33, 59, '2013-04-18 17:51:00', 'Invoice No: 7yj788  Amount: AUD 500  Deposit Date: 2013-04-25 Map term:9 is created.', 0, NULL, 'Invoice No: 7yj788  Amount: AUD 500  Deposit Date: 2013-04-25 Map term:9'),
(226, 33, 59, '2013-04-18 17:51:45', 'Invoice No: 7yj788  Amount: AUD 500.00  Deposit Date: 2013-04-25 Map term:9 is updated.', 0, NULL, 'Invoice No: 7yj788  Amount: AUD 500.00  Deposit Date: 2013-04-25 Map term:9'),
(227, 44, 59, '2013-04-18 17:54:42', 'tresu.doc is added.', 0, NULL, 'tresu.doc'),
(228, 46, 59, '2013-04-18 17:57:51', 'Invoice No: 7yj788qe3r  Amount: INR 2000  Deposit Date: 2013-04-25 Map term:4 is created.', 0, NULL, 'Invoice No: 7yj788qe3r  Amount: INR 2000  Deposit Date: 2013-04-25 Map term:4'),
(229, 46, 59, '2013-04-18 17:58:07', 'Invoice No: 7yj788qe3r  Amount: INR 1000.00  Deposit Date: 2013-04-25 Map term:4 is updated.', 0, NULL, 'Invoice No: 7yj788qe3r  Amount: INR 1000.00  Deposit Date: 2013-04-25 Map term:4'),
(230, 46, 59, '2013-04-18 18:10:53', 'payments.txt is added.', 0, NULL, 'payments.txt'),
(231, 47, 59, '2013-04-18 18:19:03', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(232, 49, 59, '2013-04-18 18:35:12', 'Desert.jpg is added.', 0, NULL, 'Desert.jpg'),
(233, 33, 59, '2013-04-18 18:35:33', 'g.bmp is added.', 0, NULL, 'g.bmp'),
(234, 33, 59, '2013-04-18 18:42:40', 'Desert.jpg is added.', 0, NULL, 'Desert.jpg'),
(235, 56, 158, '2013-04-18 18:45:40', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00056 ', 0, NULL, NULL),
(236, 56, 158, '2013-04-18 18:45:55', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00056 ', 0, NULL, NULL),
(245, 56, 158, '2013-04-18 19:13:58', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00056 ', 0, NULL, NULL),
(246, 54, 135, '2013-04-18 19:54:19', '4.jpg is added.', 0, NULL, '4.jpg'),
(247, 54, 135, '2013-04-18 19:54:22', '4.jpg is deleted.', 0, NULL, '4.jpg'),
(248, 54, 135, '2013-04-18 19:54:26', '5.jpg is added.', 0, NULL, '5.jpg'),
(249, 57, 135, '2013-04-18 19:55:41', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00057 ', 0, NULL, NULL),
(250, 58, 135, '2013-04-18 20:00:19', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00058 ', 0, NULL, NULL),
(251, 58, 135, '2013-04-18 20:00:52', '5.jpg is added.', 0, NULL, '5.jpg'),
(252, 45, 158, '2013-04-19 10:20:49', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(253, 45, 158, '2013-04-19 10:21:35', '1366347095images.jpg is added.', 0, NULL, '1366347095images.jpg'),
(254, 45, 158, '2013-04-19 10:21:37', '1366347095images.jpg is deleted.', 0, NULL, '1366347095images.jpg'),
(256, 45, 158, '2013-04-19 11:42:46', 'Project Milestone Name: Payment #1  Amount: AUD 3573573573737  Expected Date: 2013-04-23 is created.', 0, NULL, 'Project Milestone Name: Payment #1  Amount: AUD 3573573573737  Expected Date: 2013-04-23'),
(257, 54, 59, '2013-04-19 14:52:35', 'Status Changed to: Prospect Sucessfully on Lead No.00054 ', 0, NULL, NULL),
(258, 45, 59, '2013-04-19 15:23:32', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(259, 45, 59, '2013-04-19 17:38:34', 'tresu.doc is deleted.', 0, NULL, 'tresu.doc'),
(260, 45, 59, '2013-04-19 17:38:38', 'Hydrangeas.jpg is added.', 0, NULL, 'Hydrangeas.jpg'),
(261, 54, 59, '2013-04-19 17:40:03', '123456cat.jpg is added.', 0, NULL, '123456cat.jpg'),
(262, 54, 59, '2013-04-19 17:40:21', '1366373421123456cat.jpg is added.', 0, NULL, '1366373421123456cat.jpg'),
(263, 54, 139, '2013-04-19 17:41:18', 'Chrysanthemum.jpg is added.', 0, NULL, 'Chrysanthemum.jpg'),
(264, 54, 139, '2013-04-19 17:42:25', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(265, 44, 139, '2013-04-19 17:42:48', 'Hydrangeas.jpg is added.', 0, NULL, 'Hydrangeas.jpg'),
(266, 54, 139, '2013-04-19 17:46:26', '1366373786123456cat.jpg is added.', 0, NULL, '1366373786123456cat.jpg'),
(267, 54, 139, '2013-04-19 17:46:37', 'v.jpg is added.', 0, NULL, 'v.jpg'),
(268, 47, 59, '2013-04-19 18:04:57', 'Hydrangeas.jpg is added.', 0, NULL, 'Hydrangeas.jpg'),
(269, 50, 59, '2013-04-19 18:05:14', '123456cat.jpg is added.', 0, NULL, '123456cat.jpg'),
(270, 50, 59, '2013-04-19 18:11:18', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(271, 44, 139, '2013-04-19 18:20:31', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(272, 44, 139, '2013-04-19 18:20:37', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(273, 44, 139, '2013-04-19 18:20:48', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(274, 58, 59, '2013-04-19 18:32:09', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(275, 58, 59, '2013-04-19 18:32:17', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(276, 58, 59, '2013-04-19 18:33:55', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(277, 57, 59, '2013-04-19 18:58:37', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(278, 57, 59, '2013-04-19 18:58:51', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(279, 57, 59, '2013-04-19 19:09:58', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(280, 57, 59, '2013-04-19 19:10:18', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(281, 56, 59, '2013-04-19 19:36:04', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(282, 56, 59, '2013-04-19 19:51:59', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(283, 53, 59, '2013-04-19 19:57:05', '1365443724-Box-Green-16x16.png is added.', 0, NULL, '1365443724-Box-Green-16x16.png'),
(284, 58, 59, '2013-04-22 10:25:54', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(307, 60, 59, '2013-04-24 14:57:01', 'readonly.info is added.', 0, NULL, 'readonly.info'),
(308, 60, 59, '2013-04-24 14:57:41', '1366795660readonly.info is added.', 0, NULL, '1366795660readonly.info'),
(310, 55, 59, '2013-04-24 15:26:35', 'asd fas ads fa sdf<br /><br />This log has been emailed to:<br />new n', 0, NULL, NULL),
(312, 52, 59, '2013-04-24 16:24:22', 'Lead has been reassigned to:&nbsp;Sriram&nbsp;S<br />For Lead No.00052 ', 0, NULL, NULL),
(313, 52, 59, '2013-04-24 16:25:20', 'Lead has been reassigned to: Raziya&nbsp;Begum<br />For Lead No.00052 ', 0, NULL, NULL),
(314, 52, 59, '2013-04-24 16:35:06', 'Lead has been reassigned to: Nagendra P<br />For Lead No.00052 ', 0, NULL, NULL),
(328, 55, 59, '2013-04-24 17:58:42', 'Ramji BH is assigned as Project Manager.', 0, NULL, ''),
(329, 55, 59, '2013-04-24 17:58:56', 'Dinesh Anand is re-assigned as Project Manager.', 0, NULL, ''),
(330, 52, 59, '2013-04-24 18:25:51', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00052 ', 0, NULL, NULL),
(300, 56, 59, '2013-04-23 16:59:06', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(331, 52, 59, '2013-04-24 18:26:04', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00052 ', 0, NULL, NULL),
(332, 52, 59, '2013-04-24 18:26:17', 'Dinesh Anand is assigned as Project Manager.', 0, NULL, ''),
(333, 52, 59, '2013-04-24 19:34:33', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(334, 60, 139, '2013-04-24 19:35:21', '1366795660readonly.info is deleted.', 0, NULL, '1366795660readonly.info'),
(335, 60, 139, '2013-04-24 19:35:18', 'Actual Worth Amount Modified On : Apr 24, 2013 7:35 PM', 0, NULL, NULL),
(336, 60, 139, '2013-04-24 19:35:32', 'Lead Onhold Reason: Other reasons', 0, NULL, NULL),
(337, 60, 59, '2013-04-24 20:48:34', 'test email<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(338, 60, 163, '2013-04-25 10:45:36', 'Status Changed to: Prospect Sucessfully on Lead No.00060 ', 0, NULL, NULL),
(339, 60, 59, '2013-04-25 10:55:45', 'test work for this lead<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(340, 60, 59, '2013-04-25 11:02:39', 'eCRM QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(477, 37, 59, '2013-05-10 18:22:09', 'Project Milestone Name: Payment No 2  Amount: USD 2000  Expected Date: 2013-03-31 is created.', 0, NULL, 'Project Milestone Name: Payment No 2  Amount: USD 2000  Expected Date: 2013-03-31'),
(478, 37, 59, '2013-05-10 18:22:16', 'Project Milestone Name: Payment No 1  Amount: USD 1500.00  Expected Date: 2013-04-01 is updated.', 0, NULL, 'Project Milestone Name: Payment No 1  Amount: USD 1500.00  Expected Date: 2013-04-01'),
(479, 37, 59, '2013-05-10 18:22:28', 'Project Milestone Name: Payment No 1  Amount: USD 1500.00  Expected Date: 2013-02-01 is updated.', 0, NULL, 'Project Milestone Name: Payment No 1  Amount: USD 1500.00  Expected Date: 2013-02-01'),
(480, 37, 59, '2013-05-10 18:23:03', 'Invoice No: Inv 001  Amount: USD 1000  Deposit Date: 2013-04-02 Map term:17 is created.', 0, NULL, 'Invoice No: Inv 001  Amount: USD 1000  Deposit Date: 2013-04-02 Map term:17'),
(481, 37, 59, '2013-05-10 18:34:09', 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18 is created.', 0, NULL, 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18'),
(482, 37, 59, '2013-05-10 18:34:41', 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18 is updated.', 0, NULL, 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18'),
(483, 37, 59, '2013-05-10 18:35:01', 'Invoice No: Inv 003  Amount: USD 500  Deposit Date: 2013-04-24 Map term:18 is created.', 0, NULL, 'Invoice No: Inv 003  Amount: USD 500  Deposit Date: 2013-04-24 Map term:18'),
(484, 37, 59, '2013-05-10 18:35:38', 'Project Milestone Name: Payment No 3  Amount: USD 2000  Expected Date: 2013-04-10 is created.', 0, NULL, 'Project Milestone Name: Payment No 3  Amount: USD 2000  Expected Date: 2013-04-10'),
(485, 37, 59, '2013-05-10 18:45:09', 'Invoice No: Inv 003  Amount: USD 500.00  Deposit Date: 2013-04-24 Map term:18 is updated.', 0, NULL, 'Invoice No: Inv 003  Amount: USD 500.00  Deposit Date: 2013-04-24 Map term:18'),
(486, 37, 59, '2013-05-10 18:45:13', 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18 is updated.', 0, NULL, 'Invoice No: Inv 002  Amount: USD 1000.00  Deposit Date: 2013-04-10 Map term:18'),
(487, 37, 59, '2013-05-10 18:45:15', 'Invoice No: Inv 001  Amount: USD 1000.00  Deposit Date: 2013-04-02 Map term:17 is updated.', 0, NULL, 'Invoice No: Inv 001  Amount: USD 1000.00  Deposit Date: 2013-04-02 Map term:17'),
(488, 55, 59, '2013-05-10 19:15:16', 'Project Milestone Name: payment 1  Amount: INR 1000  Expected Date: 2013-05-10 is created.', 0, NULL, 'Project Milestone Name: payment 1  Amount: INR 1000  Expected Date: 2013-05-10'),
(355, 55, 59, '2013-05-03 11:59:56', 'Project Milestone Name: test  Amount: INR 100  Expected Date: 2013-03-05 is created.', 0, NULL, 'Project Milestone Name: test  Amount: INR 100  Expected Date: 2013-03-05'),
(356, 55, 59, '2013-05-03 12:00:11', 'Project Milestone Name: test  Amount: INR 200.00  Expected Date: 2013-03-05 is updated.', 0, NULL, 'Project Milestone Name: test  Amount: INR 200.00  Expected Date: 2013-03-05'),
(358, 56, 59, '2013-05-03 12:08:21', 'pr vignesh is re-assigned as Project Manager.', 0, NULL, ''),
(359, 56, 59, '2013-05-03 12:09:20', 'Sriram S is re-assigned as Project Manager.', 0, NULL, ''),
(489, 55, 59, '2013-05-10 19:16:26', 'Invoice No: inv-001  Amount: INR 250  Deposit Date: 2013-05-10 Map term:20 is created.', 0, NULL, 'Invoice No: inv-001  Amount: INR 250  Deposit Date: 2013-05-10 Map term:20'),
(490, 55, 59, '2013-05-10 19:16:48', 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20 is updated.', 0, NULL, 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20'),
(491, 55, 59, '2013-05-10 19:18:30', 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20 is updated.', 0, NULL, 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20'),
(492, 55, 59, '2013-05-10 19:25:48', 'Invoice No: inv234  Amount: INR 250  Deposit Date: 2013-05-10 Map term:20 is created.', 0, NULL, 'Invoice No: inv234  Amount: INR 250  Deposit Date: 2013-05-10 Map term:20'),
(493, 55, 59, '2013-05-10 20:13:31', 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20 is updated.', 0, NULL, 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20'),
(494, 55, 59, '2013-05-11 14:27:40', 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20 is updated.', 0, NULL, 'Invoice No: inv-001  Amount: INR 250.00  Deposit Date: 2013-05-10 Map term:20'),
(500, 55, 59, '2013-06-18 15:59:21', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(501, 55, 59, '2013-06-18 15:59:36', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(502, 55, 59, '2013-06-18 15:59:45', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(503, 55, 59, '2013-06-18 15:59:52', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(504, 49, 59, '2013-06-18 17:30:05', 'Status Changed to: Prospect Sucessfully on Lead No.00049 ', 0, NULL, NULL),
(505, 49, 59, '2013-06-18 17:30:24', 'Status Changed to: Demo Scheduled Sucessfully on Lead No.00049 ', 0, NULL, NULL),
(506, 49, 59, '2013-06-18 17:30:37', 'Status Changed to: Proposal Under Review Sucessfully on Lead No.00049 ', 0, NULL, NULL),
(507, 49, 59, '2013-06-18 17:30:55', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully on Lead No.00049 ', 0, NULL, NULL),
(508, 49, 59, '2013-06-18 17:31:10', 'Status Changed to: Declined Proposals Sucessfully on Lead No.00049 ', 0, NULL, NULL),
(509, 37, 59, '2013-06-18 17:33:15', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(510, 37, 59, '2013-06-18 18:09:43', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(511, 37, 59, '2013-06-18 18:10:22', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(512, 63, 59, '2013-06-27 15:11:56', 'Status Changed to: POC in Progress Sucessfully on Lead No.hi test leadasdf  ', 0, NULL, NULL),
(513, 63, 59, '2013-06-27 15:12:48', 'Status Changed to: Demo Scheduled Sucessfully on Lead hi test leadasdf  ', 0, NULL, NULL),
(514, 63, 59, '2013-06-27 15:13:40', 'Status Changed to: Proposal WIP Sucessfully on Lead hi test leadasdf  ', 0, NULL, NULL),
(515, 63, 59, '2013-06-27 15:14:54', 'Status Changed to: Proposal Under Review Sucessfully on Lead hi test leadasdf  ', 0, NULL, NULL),
(516, 63, 59, '2013-06-27 15:16:52', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead hi test leadasdf  ', 0, NULL, NULL),
(517, 63, 59, '2013-06-27 15:24:28', 'Status Changed to: Declined Proposals Sucessfully for the Lead- hi test leadasdf  ', 0, NULL, NULL),
(518, 63, 59, '2013-06-27 15:25:09', 'Actual Worth Amount Modified On : Jun 27, 2013 3:25 PM', 0, NULL, NULL),
(519, 63, 59, '2013-06-27 15:29:47', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - hi test leadasdf  ', 0, NULL, NULL),
(520, 63, 59, '2013-06-27 15:30:50', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - hi test leadasdf  ', 0, NULL, NULL),
(688, 63, 59, '2013-09-11 18:17:18', 'Status Change:\nProject - In Progress', 0, NULL, NULL),
(689, 63, 59, '2013-09-11 18:17:31', 'Status Change:\nProject - Completed', 0, NULL, NULL),
(687, 53, 59, '2013-09-11 17:35:16', 'Actual Worth Amount Modified On : Sep 11, 2013 5:35 PM', 0, NULL, NULL),
(685, 58, 59, '2013-09-11 16:34:32', 'test123 - Lead Deleted Sucessfully.', 0, NULL, NULL),
(686, 53, 59, '2013-09-11 17:35:30', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - HooperHolmes ', 0, NULL, NULL),
(684, 51, 59, '2013-09-11 16:31:00', 'test lead - Lead Deleted Sucessfully.', 0, NULL, NULL),
(683, 46, 59, '2013-09-11 16:30:38', 'Firm Export - Lead Deleted Sucessfully.', 0, NULL, NULL),
(527, 63, 59, '2013-06-27 17:47:37', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - hi test leadasdf  ', 0, NULL, NULL),
(528, 63, 59, '2013-06-27 17:48:23', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - hi test leadasdf  ', 0, NULL, NULL),
(529, 53, 59, '2013-07-08 14:06:34', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - HooperHolmes ', 0, NULL, NULL),
(547, 63, 59, '2013-07-16 09:43:52', 'Project Milestone Name: payment 1  Amount: SGD 1000  Expected Date: 2013-07-09 is created.', 0, NULL, 'Project Milestone Name: payment 1  Amount: SGD 1000  Expected Date: 2013-07-09'),
(548, 63, 59, '2013-07-16 09:44:59', 'Project Milestone Name: payment 1  Amount: SGD 1200.00  Expected Date: 2013-07-09 is updated.', 0, NULL, 'Project Milestone Name: payment 1  Amount: SGD 1200.00  Expected Date: 2013-07-09'),
(549, 63, 59, '2013-07-16 09:45:22', 'Invoice No: bill no 12  Amount: SGD 400  Deposit Date: 2013-07-10 Map term:22 is created.', 0, NULL, 'Invoice No: bill no 12  Amount: SGD 400  Deposit Date: 2013-07-10 Map term:22'),
(550, 63, 59, '2013-07-16 09:45:39', 'Invoice No: bill no 12  Amount: SGD 600.00  Deposit Date: 2013-07-10 Map term:22 is updated.', 0, NULL, 'Invoice No: bill no 12  Amount: SGD 600.00  Deposit Date: 2013-07-10 Map term:22'),
(551, 63, 59, '2013-07-16 09:46:10', 'Invoice No: bill no 15  Amount: SGD 200  Deposit Date: 2013-07-10 Map term:22 is created.', 0, NULL, 'Invoice No: bill no 15  Amount: SGD 200  Deposit Date: 2013-07-10 Map term:22'),
(552, 64, 59, '2013-07-17 15:52:06', 'Actual Worth Amount Modified On : Jul 17, 2013 3:52 PM', 0, NULL, NULL),
(553, 67, 59, '2013-07-17 18:55:33', 'Actual Worth Amount Modified On : Jul 17, 2013 6:55 PM', 0, NULL, NULL),
(680, 46, 59, '2013-09-11 16:26:26', 'Firm Export - Lead Deleted Sucessfully.', 0, NULL, NULL),
(681, 58, 59, '2013-09-11 16:27:05', 'test123 - Lead Deleted Sucessfully.', 0, NULL, NULL),
(682, 51, 59, '2013-09-11 16:27:32', 'test lead - Lead Deleted Sucessfully.', 0, NULL, NULL),
(556, 66, 59, '2013-07-18 15:28:56', 'Actual Worth Amount Modified On : Jul 18, 2013 3:29 PM', 0, NULL, NULL),
(690, 63, 59, '2013-09-11 18:17:38', 'Status Change:\nProject - Onhold', 0, NULL, NULL),
(558, 70, 170, '2013-07-19 19:10:56', 'colors.txt is added.', 0, NULL, 'colors.txt'),
(559, 47, 59, '2013-07-24 18:19:59', 'Lead Onhold Reason: payment delayed', 0, NULL, NULL),
(560, 47, 59, '2013-07-24 18:22:52', 'Lead Onhold Reason: Payment Delayed', 0, NULL, NULL),
(561, 47, 59, '2013-07-24 18:23:19', 'Lead Onhold Reason: Payment Delayed', 0, NULL, NULL),
(562, 47, 59, '2013-07-24 18:24:03', 'Lead Onhold Reason: Payment Delayed', 0, NULL, NULL),
(563, 70, 59, '2013-07-26 15:53:34', 'Status Changed to: Prospect Sucessfully for the Lead - Testing Application ', 0, NULL, NULL),
(564, 69, 59, '2013-07-26 15:56:27', 'Lead has been reassigned to: vignesh pr<br />For Lead No.00069 ', 0, NULL, NULL),
(565, 54, 59, '2013-07-26 16:10:11', 'Actual Worth Amount Modified On : Jul 26, 2013 4:10 PM', 0, NULL, NULL),
(566, 72, 59, '2013-07-29 19:31:11', 'Lead has been reassigned to: sathishkuamr r<br />For Lead No.00072 ', 0, NULL, NULL),
(567, 72, 59, '2013-07-29 19:48:58', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - Leads for readings ', 0, NULL, NULL),
(568, 76, 59, '2013-08-06 15:02:59', 'Status Changed to: Prospect Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(569, 76, 59, '2013-08-06 15:03:26', 'Status Changed to: POC in Progress Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(570, 76, 59, '2013-08-06 15:04:56', 'Status Changed to: POC in Progress Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(571, 76, 59, '2013-08-06 15:05:21', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(572, 76, 59, '2013-08-06 15:06:32', 'Status Changed to: Proposal WIP Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(573, 76, 59, '2013-08-06 15:46:32', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(574, 76, 59, '2013-08-06 15:54:44', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(575, 49, 59, '2013-08-07 12:39:34', 'Lead Onhold Reason: any reason for onhold', 0, NULL, NULL),
(577, 76, 59, '2013-08-07 19:15:55', 'Status Changed to: Declined Proposals Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(578, 76, 59, '2013-08-07 19:16:15', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(579, 72, 59, '2013-08-07 19:17:20', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - Leads for readings ', 0, NULL, NULL),
(580, 70, 59, '2013-08-07 19:24:01', 'Status Changed to: Proposal WIP Sucessfully for the Lead - Testing Application ', 0, NULL, NULL),
(581, 70, 59, '2013-08-07 19:24:30', 'Status Changed to: SOW under Review Sucessfully for the Lead - Testing Application ', 0, NULL, NULL),
(694, 72, 59, '2013-09-11 19:01:33', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - Leads for readings ', 0, NULL, NULL),
(693, 80, 59, '2013-09-11 18:30:31', 'Status Change:\nThe Project moved to Onhold', 0, NULL, NULL),
(691, 63, 59, '2013-09-11 18:18:49', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(692, 25, 59, '2013-09-11 18:26:06', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(585, 49, 59, '2013-08-07 19:52:29', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - IT Testing & Design ', 0, NULL, NULL),
(586, 49, 59, '2013-08-07 19:52:42', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - IT Testing & Design ', 0, NULL, NULL),
(587, 74, 59, '2013-08-07 19:53:24', 'Status Changed to: POC in Progress Sucessfully for the Lead - Leads for shankar-1 ', 0, NULL, NULL),
(588, 74, 59, '2013-08-07 19:53:35', 'Status Changed to: Proposal WIP Sucessfully for the Lead - Leads for shankar-1 ', 0, NULL, NULL),
(589, 74, 59, '2013-08-07 19:54:33', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Leads for shankar-1 ', 0, NULL, NULL),
(590, 74, 59, '2013-08-07 19:54:56', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - Leads for shankar-1 ', 0, NULL, NULL),
(591, 76, 59, '2013-08-07 20:28:23', 'Status Changed to: SOW under Review Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(592, 76, 59, '2013-08-07 20:35:45', 'Lead Onhold Reason: test', 0, NULL, NULL),
(593, 76, 59, '2013-08-07 20:35:45', 'Actual Worth Amount Modified On : Aug 7, 2013 8:36 PM', 0, NULL, NULL),
(594, 76, 59, '2013-08-07 20:37:59', 'Lead Onhold Reason: test', 0, NULL, NULL),
(595, 60, 59, '2013-08-07 20:39:50', 'Status Changed to: Proposal WIP Sucessfully for the Lead - asdf asdf asdf asdf asdf ', 0, NULL, NULL),
(596, 60, 59, '2013-08-07 20:40:38', 'Lead Onhold Reason: Other reasons', 0, NULL, NULL),
(597, 78, 59, '2013-08-07 21:08:59', 'Lead Onhold Reason: Client not responsed', 0, NULL, NULL),
(598, 76, 59, '2013-08-07 21:11:00', 'Status Changed to: Declined Proposals Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(599, 76, 59, '2013-08-08 11:57:41', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(600, 64, 59, '2013-08-08 14:50:01', 'Status Changed to: SOW under Review Sucessfully for the Lead - test customer lead ', 0, NULL, NULL),
(678, 89, 59, '2013-09-11 14:39:18', 'Status Changed to: POC in Progress Sucessfully for the Lead - Sample Lead ', 0, NULL, NULL),
(602, 79, 59, '2013-08-14 19:53:29', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - test asdfa sdfa sdfasdf ', 0, NULL, NULL),
(603, 73, 59, '2013-08-16 12:40:29', 'Status Changed to: POC in Progress Sucessfully for the Lead - Lead for Jukebox ', 0, NULL, NULL),
(604, 73, 59, '2013-08-16 16:45:53', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - Lead for Jukebox ', 0, NULL, NULL),
(605, 78, 59, '2013-08-21 10:44:30', 'file-array.txt is added.', 0, NULL, 'file-array.txt'),
(606, 78, 59, '2013-08-21 10:49:25', 'junior-resources.xlsx is added.', 0, NULL, 'junior-resources.xlsx'),
(607, 78, 59, '2013-08-21 10:49:27', 'closed-opportunities-leads.xls is added.', 0, NULL, 'closed-opportunities-leads.xls'),
(608, 78, 59, '2013-08-21 10:49:29', 'ecrm14-08.sql is added.', 0, NULL, 'ecrm14-08.sql'),
(609, 78, 59, '2013-08-21 10:49:32', 'closed-opportunities.txt is added.', 0, NULL, 'closed-opportunities.txt'),
(610, 78, 59, '2013-08-21 10:49:34', 'ecrm-tables-need-to-add-in-live.txt is added.', 0, NULL, 'ecrm-tables-need-to-add-in-live.txt'),
(611, 78, 59, '2013-08-21 10:49:40', 'ecrm-queries.txt is added.', 0, NULL, 'ecrm-queries.txt'),
(612, 78, 59, '2013-08-21 10:49:42', '1377062382ecrm-queries.txt is added.', 0, NULL, '1377062382ecrm-queries.txt'),
(613, 78, 59, '2013-08-21 10:49:45', 'phpchart.txt is added.', 0, NULL, 'phpchart.txt'),
(614, 78, 59, '2013-08-21 10:49:47', 'colors.txt is added.', 0, NULL, 'colors.txt'),
(615, 78, 59, '2013-08-21 10:49:54', 'tresu.doc is added.', 0, NULL, 'tresu.doc'),
(616, 78, 59, '2013-08-21 10:50:10', 'payngo.txt is added.', 0, NULL, 'payngo.txt'),
(617, 77, 59, '2013-08-21 12:15:52', 'Status Changed to: Prospect Sucessfully for the Lead - test leads for mid client ', 0, NULL, NULL),
(618, 77, 59, '2013-08-21 12:22:22', 'Status Changed to: POC in Progress Sucessfully for the Lead - test leads for mid client ', 0, NULL, NULL),
(619, 73, 59, '2013-08-21 12:35:04', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - Lead for Jukebox ', 0, NULL, NULL),
(620, 73, 59, '2013-08-21 12:35:25', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Lead for Jukebox ', 0, NULL, NULL),
(621, 71, 59, '2013-08-21 12:36:20', 'Status Changed to: Prospect Sucessfully for the Lead - Testing Lead for Dashboard ', 0, NULL, NULL),
(622, 71, 59, '2013-08-21 12:37:11', 'Status Changed to: POC in Progress Sucessfully for the Lead - Testing Lead for Dashboard ', 0, NULL, NULL),
(623, 71, 59, '2013-08-21 12:41:58', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - Testing Lead for Dashboard ', 0, NULL, NULL),
(624, 71, 59, '2013-08-21 12:43:55', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Testing Lead for Dashboard ', 0, NULL, NULL),
(625, 71, 59, '2013-08-21 14:02:54', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - Testing Lead for Dashboard ', 0, NULL, NULL),
(626, 76, 59, '2013-08-21 14:41:32', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(627, 77, 59, '2013-08-21 16:02:10', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - test leads for mid client ', 0, NULL, NULL),
(628, 70, 59, '2013-08-26 17:59:39', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - Testing Application ', 0, NULL, NULL),
(629, 80, 59, '2013-08-26 19:45:25', 'file-array.txt is added.', 0, NULL, 'file-array.txt'),
(630, 25, 59, '2013-08-28 14:45:55', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(631, 25, 59, '2013-08-28 14:46:01', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(632, 90, 59, '2013-08-29 11:16:40', 'rupee-fall.jpg is added.', 0, NULL, 'rupee-fall.jpg'),
(633, 90, 59, '2013-08-29 11:18:19', '\nTimeline for the project: Test Lead\n31-08-2013 : dfasdf\n<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(634, 90, 59, '2013-08-29 11:34:01', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Test Lead ', 0, NULL, NULL),
(635, 90, 59, '2013-08-29 11:34:32', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Test Lead ', 0, NULL, NULL),
(636, 90, 59, '2013-08-29 11:39:29', 'Actual Worth Amount Modified On : Aug 29, 2013 11:39 AM', 0, NULL, NULL),
(637, 90, 59, '2013-08-29 11:40:09', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - Test Lead ', 0, NULL, NULL),
(638, 90, 59, '2013-08-29 11:44:55', 'Project Milestone Name: test payment1  Amount: AUD 1200  Expected Date: 2013-08-08 is created.', 0, NULL, 'Project Milestone Name: test payment1  Amount: AUD 1200  Expected Date: 2013-08-08'),
(639, 90, 59, '2013-08-29 11:45:20', 'Project Milestone Name: test payment2  Amount: AUD 1200  Expected Date: 2013-08-31 is created.', 0, NULL, 'Project Milestone Name: test payment2  Amount: AUD 1200  Expected Date: 2013-08-31');
INSERT INTO `crms_logs` (`logid`, `jobid_fk`, `userid_fk`, `date_created`, `log_content`, `stickie`, `time_spent`, `attached_docs`) VALUES
(640, 90, 59, '2013-08-29 11:45:45', 'Invoice No: 3432  Amount: AUD 600  Deposit Date: 2013-08-22 Map term:23 is created.', 0, NULL, 'Invoice No: 3432  Amount: AUD 600  Deposit Date: 2013-08-22 Map term:23'),
(641, 90, 59, '2013-08-29 11:46:27', 'Project Milestone Name: test payment2  Amount: AUD 1200.00  Expected Date: 2013-08-31 is deleted.', 0, NULL, 'Project Milestone Name: test payment2  Amount: AUD 1200.00  Expected Date: 2013-08-31'),
(642, 90, 59, '2013-08-29 11:47:45', 'Invoice No: 234ff  Amount: AUD 600  Deposit Date: 2013-08-29 Map term:23 is created.', 0, NULL, 'Invoice No: 234ff  Amount: AUD 600  Deposit Date: 2013-08-29 Map term:23'),
(643, 89, 59, '2013-09-02 16:40:12', 'lead-stage.txt is added.', 0, NULL, 'lead-stage.txt'),
(644, 63, 59, '2013-09-02 19:34:36', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(645, 63, 59, '2013-09-02 19:34:51', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(646, 63, 59, '2013-09-02 19:37:58', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(647, 63, 59, '2013-09-02 19:38:17', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(648, 89, 59, '2013-09-04 11:39:30', 'Status Changed to: Prospect Sucessfully for the Lead - Sample Lead ', 0, NULL, NULL),
(649, 45, 59, '2013-09-04 15:56:04', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(650, 45, 59, '2013-09-04 15:56:22', 'Status Change:\nThe Project moved to Cancelled', 0, NULL, NULL),
(651, 88, 59, '2013-09-04 17:26:14', 'Status Changed to: Prospect Sucessfully for the Lead - gowir ', 0, NULL, NULL),
(652, 87, 59, '2013-09-04 17:27:39', 'Actual Worth Amount Modified On : Sep 4, 2013 5:28 PM', 0, NULL, NULL),
(653, 87, 59, '2013-09-04 17:28:09', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - test ', 0, NULL, NULL),
(654, 45, 59, '2013-09-04 19:13:01', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(655, 52, 59, '2013-09-04 19:13:23', 'Status Change:\nThe Project moved to Completed', 0, NULL, NULL),
(656, 55, 59, '2013-09-04 19:14:08', 'Status Change:\nThe Project moved to On Hold', 0, NULL, NULL),
(657, 76, 59, '2013-09-05 17:42:08', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - mums shopping cart paypal extension ', 0, NULL, NULL),
(658, 80, 59, '2013-09-05 17:42:37', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(659, 80, 59, '2013-09-05 17:42:47', 'Status Changed to: Declined Proposals Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(660, 80, 59, '2013-09-05 17:42:56', 'Status Changed to: Proposal Accepted. Convert to SOW Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(661, 80, 59, '2013-09-05 17:43:06', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(662, 80, 59, '2013-09-05 17:43:27', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(663, 80, 59, '2013-09-05 17:43:25', 'Actual Worth Amount Modified On : Sep 5, 2013 5:43 PM', 0, NULL, NULL),
(664, 80, 59, '2013-09-05 17:43:54', 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully for the Lead - test readings leads ', 0, NULL, NULL),
(665, 82, 59, '2013-09-05 18:07:36', 'Status Changed to: Proposal WIP Sucessfully for the Lead - ta sdfas asdf asdf ', 0, NULL, NULL),
(666, 82, 59, '2013-09-05 18:07:47', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - ta sdfas asdf asdf ', 0, NULL, NULL),
(667, 82, 59, '2013-09-05 18:07:57', 'Status Changed to: SOW Sent to Client. Awaiting Sign off Sucessfully for the Lead - ta sdfas asdf asdf ', 0, NULL, NULL),
(668, 82, 59, '2013-09-05 18:07:54', 'Actual Worth Amount Modified On : Sep 5, 2013 6:08 PM', 0, NULL, NULL),
(669, 82, 59, '2013-09-05 18:08:24', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - ta sdfas asdf asdf ', 0, NULL, NULL),
(670, 87, 59, '2013-09-05 18:18:59', 'Status Change:\nStatus Successfully Changed', 0, NULL, NULL),
(671, 87, 59, '2013-09-05 18:22:05', 'Status Change:\nStatus Successfully Changed', 0, NULL, NULL),
(677, 89, 59, '2013-09-06 17:35:08', 'Actual Worth Amount Modified On : Sep 6, 2013 5:35 PM', 0, NULL, NULL),
(701, 52, 59, '2013-09-12 12:19:51', 'Status Change:\nInactive', 0, NULL, NULL),
(702, 63, 59, '2013-09-12 12:20:18', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(700, 82, 59, '2013-09-11 20:43:00', 'The Lead "ta sdfas asdf asdf" is Successfully Moved to Project.', 0, NULL, NULL),
(704, 63, 59, '2013-09-12 14:45:04', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(705, 55, 59, '2013-09-12 14:45:49', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(706, 72, 59, '2013-09-12 14:46:52', 'Status Change:\nThe Project moved to Onhold', 0, NULL, NULL),
(707, 91, 59, '2013-09-12 14:58:48', 'Document-Write-icon.png is added.', 0, NULL, 'Document-Write-icon.png'),
(708, 91, 59, '2013-09-12 15:16:48', 'Lead Onhold Reason: test', 0, NULL, NULL),
(709, 91, 59, '2013-09-12 15:17:37', 'Lead Onhold Reason: test', 0, NULL, NULL),
(710, 91, 59, '2013-09-12 15:18:03', 'Lead Onhold Reason: test fasdf', 0, NULL, NULL),
(711, 91, 59, '2013-09-12 15:19:24', 'Lead Onhold Reason: test fasdf', 0, NULL, NULL),
(712, 91, 59, '2013-09-12 15:21:25', 'The Lead "Sandal powder lead" is Successfully Moved to Project.', 0, NULL, NULL),
(716, 91, 59, '2013-09-12 16:42:39', 'Status Change:\nThe Project moved to Inactive', 0, NULL, NULL),
(717, 91, 59, '2013-09-12 16:43:06', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(718, 92, 59, '2013-09-16 12:41:12', 'Actual Worth Amount Modified On : Sep 16, 2013 12:41 PM', 0, NULL, NULL),
(719, 92, 59, '2013-09-16 12:46:42', 'Actual Worth Amount Modified On : Sep 16, 2013 12:47 PM', 0, NULL, NULL),
(720, 83, 59, '2013-09-16 12:54:44', 'Actual Worth Amount Modified On : Sep 16, 2013 12:55 PM', 0, NULL, NULL),
(721, 93, 158, '2013-09-16 13:39:48', 'Actual Worth Amount Modified On : Sep 16, 2013 1:40 PM', 0, NULL, NULL),
(722, 93, 158, '2013-09-16 13:41:08', 'Actual Worth Amount Modified On : Sep 16, 2013 1:41 PM', 0, NULL, NULL),
(723, 93, 59, '2013-09-16 13:43:06', 'Lead has been reassigned to: Ramakrishnan V<br />For Lead No.00093 ', 0, NULL, NULL),
(724, 93, 59, '2013-09-16 17:54:59', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - Testing Lead for SSM Gp ', 0, NULL, NULL),
(725, 93, 59, '2013-09-16 17:55:21', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - Testing Lead for SSM Gp ', 0, NULL, NULL),
(726, 93, 59, '2013-09-16 19:01:17', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - Testing Lead for SSM Gp ', 0, NULL, NULL),
(727, 92, 59, '2013-09-16 19:06:50', 'Status Changed to: Prospect Sucessfully for the Lead - testing ', 0, NULL, NULL),
(728, 92, 59, '2013-09-16 19:09:25', 'Status Changed to: POC in Progress Sucessfully for the Lead - testing ', 0, NULL, NULL),
(729, 92, 59, '2013-09-16 19:15:56', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - testing ', 0, NULL, NULL),
(730, 92, 59, '2013-09-16 19:18:18', 'Status Changed to: Proposal WIP Sucessfully for the Lead - testing ', 0, NULL, NULL),
(731, 92, 59, '2013-09-16 19:20:15', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - testing ', 0, NULL, NULL),
(732, 92, 59, '2013-09-16 19:23:02', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - testing ', 0, NULL, NULL),
(733, 92, 59, '2013-09-16 19:23:18', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - testing ', 0, NULL, NULL),
(734, 92, 59, '2013-09-16 19:23:36', 'The Lead "testing" is Successfully Moved to Project.', 0, NULL, NULL),
(735, 94, 173, '2013-09-16 19:25:56', 'Actual Worth Amount Modified On : Sep 16, 2013 7:26 PM', 0, NULL, NULL),
(736, 94, 173, '2013-09-16 19:26:27', 'Status Changed to: Prospect Sucessfully for the Lead - Anbu Testing lead ', 0, NULL, NULL),
(737, 94, 173, '2013-09-16 19:26:40', 'Status Changed to: Proposal WIP Sucessfully for the Lead - Anbu Testing lead ', 0, NULL, NULL),
(738, 94, 173, '2013-09-16 19:26:56', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - Anbu Testing lead ', 0, NULL, NULL),
(739, 94, 173, '2013-09-16 19:27:41', 'The Lead "Anbu Testing lead" is Successfully Moved to Project.', 0, NULL, NULL),
(740, 93, 59, '2013-09-18 11:03:31', 'Status Changed to: SOW Approved. Create Project Charter Sucessfully for the Lead - Testing Lead for SSM Gp ', 0, NULL, NULL),
(741, 84, 59, '2013-09-19 16:54:56', 'Actual Worth Amount Modified On : Sep 19, 2013 4:55 PM', 0, NULL, NULL),
(742, 84, 59, '2013-09-20 15:28:41', 'Status Changed to: POC in Progress Sucessfully for the Lead - tstst ', 0, NULL, NULL),
(743, 84, 59, '2013-09-20 15:29:06', 'The Lead "tstst" is Successfully Moved to Project.', 0, NULL, NULL),
(744, 95, 59, '2013-09-20 15:35:47', 'Status Changed to: Prospect Sucessfully for the Lead - fan lead ', 0, NULL, NULL),
(745, 95, 59, '2013-09-20 15:35:34', 'Actual Worth Amount Modified On : Sep 20, 2013 3:35 PM', 0, NULL, NULL),
(746, 95, 59, '2013-09-20 15:36:35', 'Status Changed to: POC in Progress Sucessfully for the Lead - fan lead ', 0, NULL, NULL),
(747, 95, 59, '2013-09-20 15:37:06', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - fan lead ', 0, NULL, NULL),
(748, 95, 59, '2013-09-20 15:37:23', 'The Lead "fan lead" is Successfully Moved to Project.', 0, NULL, NULL),
(749, 93, 59, '2013-09-23 11:53:09', 'electra.png is added.', 0, NULL, 'electra.png'),
(750, 73, 59, '2013-09-23 15:29:03', 'Actual Worth Amount Modified On : Sep 23, 2013 3:29 PM', 0, NULL, NULL),
(751, 85, 59, '2013-09-24 15:10:40', 'Actual Worth Amount Modified On : Sep 24, 2013 3:11 PM', 0, NULL, NULL),
(752, 85, 59, '2013-09-24 15:11:04', 'Lead has been reassigned to: Ganesh Kum R<br />For Lead No.00085 ', 0, NULL, NULL),
(753, 78, 59, '2013-09-24 15:36:40', 'Lead Onhold Reason: Client not responsed', 0, NULL, NULL),
(754, 78, 59, '2013-09-24 15:36:40', 'Actual Worth Amount Modified On : Sep 24, 2013 3:37 PM', 0, NULL, NULL),
(755, 78, 59, '2013-09-24 15:37:04', 'Lead has been reassigned to: Nagendra P<br />For Lead No.00078 ', 0, NULL, NULL),
(759, 88, 158, '2013-09-27 12:51:13', 'Actual Worth Amount Modified On : Sep 27, 2013 12:51 PM', 0, NULL, NULL),
(760, 88, 59, '2013-09-27 15:04:07', 'Lead Owner has been reassigned to: anbu r<br />For Lead No.00088 ', 0, NULL, NULL),
(761, 64, 155, '2013-09-27 16:32:13', 'Lead has been reassigned to: Sriram S<br />For Lead No.00064 ', 0, NULL, NULL),
(762, 94, 59, '2013-10-09 15:21:39', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(763, 94, 59, '2013-10-09 15:21:51', 'Status Change:\nThe Project moved to Onhold', 0, NULL, NULL),
(764, 94, 59, '2013-10-09 15:22:01', 'Status Change:\nThe Project moved to Inactive', 0, NULL, NULL),
(765, 94, 59, '2013-10-09 15:22:14', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(766, 82, 59, '2013-10-09 19:07:58', 'test mail from ecrm', 0, NULL, NULL),
(767, 82, 59, '2013-10-09 19:09:22', 'test email<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(771, 36, 59, '2013-10-11 14:29:31', 'Status Change:\nThe Project moved to Onhold', 0, NULL, NULL),
(772, 84, 59, '2013-10-11 14:40:26', 'Project Milestone Name: Milestone 1  Amount: SGD 5000  Expected Date: 2013-10-10 is created.', 0, NULL, 'Project Milestone Name: Milestone 1  Amount: SGD 5000  Expected Date: 2013-10-10'),
(773, 84, 59, '2013-10-11 14:41:44', 'Invoice No: INV009  Amount: SGD 3000  Deposit Date: 2013-10-11 Map term:24 is created.', 0, NULL, 'Invoice No: INV009  Amount: SGD 3000  Deposit Date: 2013-10-11 Map term:24'),
(774, 84, 59, '2013-10-11 15:01:35', 'Invoice No: INV010  Amount: SGD 2000  Deposit Date: 2013-10-11 Map term:24 is created.', 0, NULL, 'Invoice No: INV010  Amount: SGD 2000  Deposit Date: 2013-10-11 Map term:24'),
(775, 84, 59, '2013-10-11 15:02:53', 'Invoice No: INV010  Amount: SGD 2000.00  Deposit Date: 2013-10-11 Map term:24 is updated.', 0, NULL, 'Invoice No: INV010  Amount: SGD 2000.00  Deposit Date: 2013-10-11 Map term:24'),
(776, 84, 59, '2013-10-11 15:09:11', 'Project Milestone Name: Milestone 2  Amount: SGD 5000  Expected Date: 2013-10-11 is created.', 0, NULL, 'Project Milestone Name: Milestone 2  Amount: SGD 5000  Expected Date: 2013-10-11'),
(777, 84, 59, '2013-10-11 15:12:22', 'Invoice No: INV011  Amount: SGD 2000  Deposit Date: 2013-10-11 Map term:25 is created.', 0, NULL, 'Invoice No: INV011  Amount: SGD 2000  Deposit Date: 2013-10-11 Map term:25'),
(778, 84, 59, '2013-10-11 15:12:52', 'Invoice No: INV011  Amount: SGD 2500.00  Deposit Date: 2013-10-11 Map term:25 is updated.', 0, NULL, 'Invoice No: INV011  Amount: SGD 2500.00  Deposit Date: 2013-10-11 Map term:25'),
(924, 95, 59, '2013-10-28 14:33:05', 'Invoice No: INV0025  Amount: INR 2000  Deposit Date: 2013-10-09 Map term:26 is created.', 0, NULL, 'Invoice No: INV0025  Amount: INR 2000  Deposit Date: 2013-10-09 Map term:26'),
(925, 95, 59, '2013-10-28 15:35:59', 'Invoice No: INV012  Amount: INR 1000.00 Deposit Date: 2013-10-02 Map term:26', 0, NULL, NULL),
(926, 95, 59, '2013-10-28 15:37:36', 'Invoice No: INV012  Amount: INR 1500.00 Deposit Date: 2013-10-02', 0, NULL, NULL),
(927, 95, 59, '2013-10-28 15:43:18', 'Project Milestone Name: Milestone 01  Amount: INR 6000.00  Expected Date: 2013-10-01', 0, NULL, NULL),
(782, 73, 59, '2013-10-17 10:50:18', 'eCRM QC Officer Log Check - All Appears OK', 0, NULL, NULL),
(973, 99, 59, '2013-10-29 13:57:06', 'Actual Worth Amount Modified On : Oct 29, 2013 1:57 PM', 0, NULL, NULL),
(972, 95, 59, '2013-10-29 11:25:31', 'Invoice No: INV0025  Amount: INR 1500.00 Deposit Date: 2013-10-28', 0, NULL, NULL),
(970, 95, 59, '2013-10-29 11:19:15', 'Invoice No: INV012  Amount: INR 1300.00 Deposit Date: 2013-10-15', 0, NULL, NULL),
(971, 95, 59, '2013-10-29 11:22:57', 'Invoice No: INV0025  Amount: INR 1000.00 Deposit Date: 2013-10-28 is Created.', 0, NULL, NULL),
(969, 95, 59, '2013-10-29 11:02:55', 'Invoice No: INV012  Amount: INR 1200 Deposit Date: 2013-10-15 is Created.', 0, NULL, NULL),
(967, 95, 59, '2013-10-29 11:01:56', 'Project Milestone Name: Milestone 02  Amount: INR 1500.00  Expected Date: 2013-10-22', 0, NULL, NULL),
(968, 95, 59, '2013-10-29 11:02:11', 'Project Milestone Name: Milestone 03  Amount: INR 900.00  Expected Date: 2013-10-01', 0, NULL, NULL),
(964, 95, 59, '2013-10-28 20:36:03', '123456cat.jpg is added.', 0, NULL, '123456cat.jpg'),
(965, 95, 59, '2013-10-28 20:36:12', '123456cat.jpg is deleted.', 0, NULL, '123456cat.jpg'),
(966, 95, 59, '2013-10-29 11:01:46', 'Invoice No: atest  Amount: INR 4500.00 Deposit Date: 2013-10-10', 0, NULL, NULL),
(963, 85, 59, '2013-10-28 20:35:10', 'dsfa sdf asdf asdf asdf asdf asdfasdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdfwe4rq htryer qwerakjadhf kahsdfkyiuero lajsdlkfj lasdjfl jasdlfj alsdfoiulajdf lasdof ujlasdjfk adsifj lasdjf;ljaskd foa sdljfl jasdlfj<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution, vignesh pr, giri@gr.com, raamsri14@gmail.com', 0, NULL, NULL),
(961, 85, 59, '2013-10-28 20:24:26', 'df asdf asdfas dasd fasdfasdf', 0, NULL, NULL),
(962, 85, 59, '2013-10-28 20:33:32', 'asdf asdf asdfasdf asdf asdf asf asdf asdf asdfasdf asdf<br /><br />This log has been emailed to:<br />vignesh pr', 0, NULL, NULL),
(957, 85, 59, '2013-10-28 20:13:15', 'terwer wer', 0, NULL, NULL),
(958, 95, 59, '2013-10-28 20:15:05', 'a sdf asdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S, vignesh pr', 0, NULL, NULL),
(959, 85, 59, '2013-10-28 20:16:41', 'a sdf asdf asdfas', 0, NULL, NULL),
(960, 85, 59, '2013-10-28 20:19:42', 'sda as asf asdfasdf', 0, NULL, NULL),
(954, 95, 59, '2013-10-28 20:08:00', 'sf gasd fasdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(955, 85, 59, '2013-10-28 20:11:27', 'sdf asdf asdf', 0, NULL, NULL),
(956, 85, 59, '2013-10-28 20:12:07', 's dfas dsa asddf asdf asdf', 0, NULL, NULL),
(953, 95, 59, '2013-10-28 19:32:18', 'a fas asdf asdfasdf<br /><br />This log has been emailed to:<br />vignesh pr, ushafan@irish.irs, raamsri14@gmail.com', 0, NULL, NULL),
(888, 78, 59, '2013-10-25 15:15:43', 'Actual Worth Amount Modified On : Oct 25, 2013 3:16 PM', 0, NULL, NULL),
(887, 78, 59, '2013-10-25 15:15:43', 'Lead Onhold Reason: Client not responsed', 0, NULL, NULL),
(952, 95, 59, '2013-10-28 19:31:32', 'resf er aesr asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(949, 95, 59, '2013-10-28 18:22:11', 'Invoice No: atest  Amount: INR 3500.00 Deposit Date: 2013-10-10 is Created.', 0, NULL, NULL),
(950, 95, 59, '2013-10-28 18:27:13', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(951, 95, 59, '2013-10-28 18:27:20', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(875, 83, 59, '2013-10-23 15:41:30', 'Actual Worth Amount Modified On : Oct 23, 2013 3:41 PM', 0, NULL, NULL),
(876, 83, 59, '2013-10-23 15:41:54', 'Lead has been Re-assigned to: vignesh pr<br />For Lead No.00083 ', 0, NULL, NULL),
(877, 83, 59, '2013-10-23 15:53:02', 'The Lead "test govind" is Successfully Moved to Project.', 0, NULL, NULL),
(878, 93, 59, '2013-10-23 18:47:54', 'Status Changed to: Project Charter Approved. Sucessfully for the Lead - Testing Lead for SSM Gp ', 0, NULL, NULL),
(879, 61, 59, '2013-10-23 19:51:03', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(880, 61, 59, '2013-10-23 19:54:23', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(881, 93, 59, '2013-10-24 14:40:26', 'temp.txt is added.', 0, NULL, 'temp.txt'),
(868, 89, 59, '2013-10-22 17:35:22', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - Sample Lead ', 0, NULL, NULL),
(869, 89, 59, '2013-10-22 17:36:45', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - Sample Lead ', 0, NULL, NULL),
(870, 89, 59, '2013-10-22 19:12:17', 'tresu.doc is added.', 0, NULL, 'tresu.doc'),
(974, 98, 59, '2013-10-29 15:27:36', 'v.jpg is added.', 0, NULL, 'v.jpg'),
(975, 98, 59, '2013-10-29 15:27:55', 'test d asdf asdf asdf asdf asdf asdf<br /><br />This log has been emailed to:<br />vignesh pr', 0, NULL, NULL),
(976, 98, 59, '2013-10-29 15:28:28', 'Actual Worth Amount Modified On : Oct 29, 2013 3:28 PM', 0, NULL, NULL),
(977, 98, 59, '2013-10-29 15:28:52', 'Lead has been Re-assigned to: Ramakrishnan V<br />For Lead No.00098 ', 0, NULL, NULL),
(978, 98, 59, '2013-10-29 15:28:37', 'Actual Worth Amount Modified On : Oct 29, 2013 3:29 PM', 0, NULL, NULL),
(979, 98, 59, '2013-10-29 15:29:01', 'Lead Owner has been Re-assigned to: Sriram S<br />For Lead No.00098 ', 0, NULL, NULL),
(980, 99, 59, '2013-10-29 16:26:20', 'Actual Worth Amount Modified On : Oct 29, 2013 4:26 PM', 0, NULL, NULL),
(981, 99, 59, '2013-10-29 16:26:44', 'Lead has been Re-assigned to: Ramji B<br />For Lead No.00099 ', 0, NULL, NULL),
(982, 99, 59, '2013-10-29 16:26:47', 'Actual Worth Amount Modified On : Oct 29, 2013 4:27 PM', 0, NULL, NULL),
(983, 99, 59, '2013-10-29 16:27:11', 'Lead Owner has been Re-assigned to: Prem Anand<br />For Lead No.00099 ', 0, NULL, NULL),
(984, 95, 59, '2013-10-29 19:39:28', 'test asd fasdfasdf asdf asd asdf asdfaslkaj sdlfjl ajsdfljlkajsdfljlaf\\ntest asd fasdfasdf asdf asd asdf asdfaslkaj sdlfjl ajsdfljlkajsdfljlaf\\ndaskfj ltest asd fasdfasdf asdf asd asdf asdfaslkaj sdlfjl ajsdfljlkajsdfljlaf\\''l\\nlkap[\\''we kf;test asd fasdfasdf asdf asd asdf asdfaslkaj sdlfjl ajsdfljlkajsdfljlaf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(985, 98, 59, '2013-10-29 20:53:10', 'Actual Worth Amount Modified On : Oct 29, 2013 8:53 PM', 0, NULL, NULL),
(986, 98, 59, '2013-10-29 20:53:34', 'Lead has been Re-assigned to: Sriram S<br />For Lead No.00098 ', 0, NULL, NULL),
(987, 98, 59, '2013-10-29 20:53:24', 'Actual Worth Amount Modified On : Oct 29, 2013 8:53 PM', 0, NULL, NULL),
(988, 98, 59, '2013-10-29 20:53:48', 'Lead has been Re-assigned to: Ramakrishnan V<br />For Lead No.00098 ', 0, NULL, NULL),
(989, 98, 59, '2013-10-29 20:59:21', 'Status Changed to: Prospect Sucessfully for the Lead - tttttttt ', 0, NULL, NULL),
(990, 98, 59, '2013-10-30 14:42:15', 'april.jpg is added.', 0, NULL, 'april.jpg'),
(991, 98, 59, '2013-10-30 16:13:37', 'v.jpg is deleted.', 0, NULL, 'v.jpg'),
(992, 98, 59, '2013-10-30 19:05:38', '123456cat.jpg is added.', 0, NULL, '123456cat.jpg'),
(993, 98, 59, '2013-10-30 20:32:51', '\\nTimeline for the project: tttttttt\\n30-10-2013 : rerqwer qwer\\n<br /><br />This log has been emailed to:<br />Sriram S, vignesh pr', 0, NULL, NULL),
(994, 98, 59, '2013-10-30 20:45:30', 'asd fas dfa sdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(995, 98, 59, '2013-10-30 20:48:04', 'sdf asdf asdf asdf asdfasdf', 0, NULL, NULL),
(996, 98, 59, '2013-10-30 21:00:45', 'asdf asdf asdf asd fasdf', 0, NULL, NULL),
(997, 98, 59, '2013-10-30 21:01:56', 'as df asdf asdf sadf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(998, 98, 59, '2013-10-31 10:17:15', '\\nTimeline for the project: tttttttt\\n30-10-2013 : tewtwt \\n', 0, NULL, NULL),
(999, 50, 59, '2013-10-31 16:54:07', 'Lead Onhold Reason: payment delayed', 0, NULL, NULL),
(1000, 93, 59, '2013-10-31 17:21:06', ' asdf asdf asdf asdf asdf asdf asdf asdfasdfasd fasd f<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1001, 93, 59, '2013-10-31 17:25:07', 'asdf asdf sadf sadf asd fasdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1002, 93, 59, '2013-10-31 17:26:59', ' asdf asdf asdf asdfas dfasdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1003, 93, 59, '2013-10-31 17:29:13', 'asdf sadf asd sadfa sdfa sdf asdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1004, 93, 59, '2013-10-31 17:29:44', 'dsf asdf sadf sadf asdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1005, 93, 59, '2013-10-31 17:30:42', 'reewweewwweew', 0, NULL, NULL),
(1006, 93, 59, '2013-10-31 17:32:45', 'tesstssssssss<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1007, 93, 59, '2013-10-31 17:33:08', 'sadf asdf asdf asdfasdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1008, 93, 59, '2013-10-31 17:34:37', 'test test test', 0, NULL, NULL),
(1009, 93, 59, '2013-10-31 17:35:47', 'qyery queyr query query qyery', 0, NULL, NULL),
(1010, 93, 59, '2013-10-31 17:39:53', 'testwtwwe etwer ewrqwer', 0, NULL, NULL),
(1011, 93, 59, '2013-10-31 17:43:08', 'yyyyyy y y y y y y<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1012, 93, 59, '2013-10-31 17:43:50', 'tewer wer wer wer werwr', 0, NULL, NULL),
(1013, 93, 59, '2013-10-31 17:47:54', 'tewew rew ewr we rweewr', 0, NULL, NULL),
(1014, 93, 59, '2013-10-31 17:53:27', 'tewtwe wer wer werwerwr ewrwqe<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1015, 93, 59, '2013-10-31 17:54:23', 'tewr tew tewe teww ', 0, NULL, NULL),
(1016, 93, 59, '2013-10-31 17:55:12', 'ferrtretret<br /><br />This log has been emailed to:<br />Ganesh Kum R', 0, NULL, NULL),
(1017, 93, 59, '2013-10-31 17:57:38', 'werewwrwrewr<br /><br />This log has been emailed to:<br />Ganesh Kum R', 0, NULL, NULL),
(1018, 93, 59, '2013-10-31 17:58:56', 'adrewrewrewr<br /><br />This log has been emailed to:<br />new n', 0, NULL, NULL),
(1019, 93, 59, '2013-10-31 18:00:24', 'qwewqewqewqeqwe<br /><br />This log has been emailed to:<br />new n', 0, NULL, NULL),
(1020, 93, 59, '2013-10-31 18:01:03', 'saewqrwerwwe<br /><br />This log has been emailed to:<br />Prem Anand', 0, NULL, NULL),
(1021, 93, 59, '2013-10-31 18:02:06', 'sadadaddasdd<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(1022, 93, 59, '2013-10-31 18:03:17', 'aedasdasdasd<br /><br />This log has been emailed to:<br />Admin eNoah - iSolution', 0, NULL, NULL),
(1023, 93, 59, '2013-10-31 18:03:34', 'sdfdsfsfdsfdsfdsf 111111111111111111', 0, NULL, NULL),
(1024, 93, 59, '2013-10-31 18:05:27', 'tewewr wer wer', 0, NULL, NULL),
(1025, 93, 59, '2013-10-31 18:06:15', 'tewe rwe we', 0, NULL, NULL),
(1026, 101, 59, '2013-10-31 18:15:07', 'Actual Worth Amount Modified On : Oct 31, 2013 6:15 PM', 0, NULL, NULL),
(1027, 101, 59, '2013-10-31 18:15:38', 'Status Changed to: Prospect Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1028, 101, 59, '2013-10-31 18:22:24', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1029, 101, 59, '2013-10-31 18:25:32', 'Status Changed to: POC in Progress Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1030, 101, 59, '2013-10-31 18:25:52', 'Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1031, 101, 59, '2013-10-31 18:26:28', 'Status Changed to: Proposal WIP Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1032, 85, 59, '2013-10-31 18:29:33', 'Status Changed to: Prospect Sucessfully for the Lead - test girp ', 0, NULL, NULL),
(1033, 85, 59, '2013-10-31 18:30:05', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - test girp ', 0, NULL, NULL),
(1034, 95, 59, '2013-10-31 18:47:26', '\\nTimeline for the project: fan lead\\n30-10-2013 : te4we rwer\\n', 0, NULL, NULL),
(1035, 95, 59, '2013-10-31 18:47:41', 'Project Milestone Name: tew  Amount: INR 1222  Expected Date: 2013-10-25', 0, NULL, NULL),
(1036, 95, 59, '2013-10-31 18:48:25', 'Invoice No: inh54  Amount: INR 1222 Deposit Date: 2013-10-31 is Created.', 0, NULL, NULL),
(1037, 79, 59, '2013-11-04 11:09:47', 'Status Change:\nThe Project moved to Inactive', 0, NULL, NULL),
(1038, 79, 59, '2013-11-04 11:09:50', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(1039, 79, 59, '2013-11-04 11:10:02', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(1040, 79, 59, '2013-11-04 11:10:12', 'Status Change:\nThe Project moved to Onhold', 0, NULL, NULL),
(1041, 79, 59, '2013-11-04 11:10:14', 'Status Change:\nThe Project moved to In Progress', 0, NULL, NULL),
(1042, 79, 59, '2013-11-04 11:11:34', 'Status Change:\nThe Project moved to Completed ', 0, NULL, NULL),
(1043, 79, 59, '2013-11-04 12:34:32', 'Project Milestone Name: tewt milestone 01  Amount:  1200  Expected Date: 2013-11-12', 0, NULL, NULL),
(1044, 79, 59, '2013-11-04 12:34:58', 'Invoice No: inh54s  Amount: USD 1200 Deposit Date: 2013-11-03 is Created.', 0, NULL, NULL),
(1045, 79, 59, '2013-11-04 12:35:49', 'tewt ew adsfasfd<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1046, 79, 59, '2013-11-04 12:40:43', 'tesd tew wer qwer qwerq wer<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1047, 79, 59, '2013-11-04 12:42:26', 'fda sdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1048, 79, 59, '2013-11-04 12:44:35', 'tesx 111<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1049, 79, 59, '2013-11-04 12:45:16', 'test 2222<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1050, 79, 59, '2013-11-04 12:47:31', 'test 3333<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1051, 79, 59, '2013-11-04 12:48:10', 'test 444<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1052, 79, 59, '2013-11-04 12:49:03', 'test 5555<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1053, 79, 59, '2013-11-04 12:49:19', 'eSmart QC Officer Log Check - All Appears OK', 0, NULL, NULL),
(1054, 79, 59, '2013-11-04 12:49:25', 'eSmart QC Officer Log Check - All Appears OK', 0, NULL, NULL),
(1055, 79, 59, '2013-11-04 12:49:43', 'eSmart QC Officer Log Check - All Appears OK<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1056, 101, 59, '2013-11-04 15:16:00', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(1057, 101, 59, '2013-11-04 15:16:34', '\\nTimeline for the project: test lead for testing\\n25-11-2013 : rerqwer qwer\\n<br /><br />This log has been emailed to:<br />vignesh pr', 0, NULL, NULL),
(1058, 101, 59, '2013-11-04 16:05:38', '1383561338images.jpg is added.', 0, NULL, '1383561338images.jpg'),
(1059, 101, 59, '2013-11-04 16:44:24', 'Status Changed to: Proposal Under Review Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1060, 54, 59, '2013-11-04 19:50:13', 'images.jpg is added.', 0, NULL, 'images.jpg'),
(1061, 54, 59, '2013-11-04 19:50:49', 'images.jpg is deleted.', 0, NULL, 'images.jpg'),
(1062, 54, 59, '2013-11-04 19:50:56', 'imagesCACXM3R2.jpg is added.', 0, NULL, 'imagesCACXM3R2.jpg'),
(1063, 101, 59, '2013-11-04 19:53:25', 'Lead-Dashboard.xls is added.', 0, NULL, 'Lead-Dashboard.xls'),
(1064, 93, 59, '2013-11-04 20:08:54', 'tewtw erwe we rwer wer<br /><br />This log has been emailed to:<br />Sriram S, sswami@enoahisolution.com, ssriram@enoahisolution.com', 0, NULL, NULL),
(1065, 101, 59, '2013-11-05 12:43:41', 'Hydrangeas.jpg is added.', 0, NULL, 'Hydrangeas.jpg'),
(1066, 101, 59, '2013-11-05 12:45:31', 'tewt df asdf asdf asdfasdf<br /><br />This log has been emailed to:<br />vignesh pr, sswami@enoahisolution.com, raamsri14@gmail.com', 0, NULL, NULL),
(1067, 101, 59, '2013-11-05 18:40:44', 'asdf asd fasd fasdf<br /><br />This log has been emailed to:<br />vignesh pr', 0, NULL, NULL),
(1068, 98, 59, '2013-11-05 19:02:49', 'a sdf asdf asdf asdf<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1069, 98, 59, '2013-11-05 19:06:40', 'test 123123123123123123123<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1070, 98, 59, '2013-11-05 19:10:25', 'asd fas df asdfasdf', 0, NULL, NULL),
(1071, 98, 59, '2013-11-05 19:13:37', 'asdf asdf asdf asdf asdf asdf asdf asdfasdf', 0, NULL, NULL),
(1072, 98, 59, '2013-11-05 19:57:52', 'A SDA SDF ASDF ASDFASDFASDF<br /><br />This log has been emailed to:<br />Sriram S, Ramakrishnan V', 0, NULL, NULL),
(1073, 98, 59, '2013-11-05 20:02:31', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.<br /><br />This log has been emailed to:<br />Surendar K, Sriram S, vignesh pr, Ramakrishnan V, ssriram@enoahisolution.com, raamsri14@gmail.com, shan62@gmail.com', 0, NULL, NULL),
(1093, 102, 59, '2013-11-06 09:47:17', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra, orci in eleifend sodales, dolor velit malesuada magna, vel auctor lectus magna vel dolor. Nullam id placerat lorem, ultrices pulvinar nulla. Morbi rutrum lobortis erat fringilla fringilla. In ultricies risus felis, id interdum nibh venenatis sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec, vulputate in tellus. Aenean et mollis eros. Praesent ullamcorper neque libero. <br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1092, 102, 59, '2013-11-06 09:43:53', 'tewt wer qwer qwer qwer<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1091, 77, 59, '2013-11-06 09:36:29', 'Lead Owner has been Re-assigned to: Ramji B<br />For Lead No.00077 ', 0, NULL, NULL),
(1090, 77, 59, '2013-11-06 09:36:05', 'Actual Worth Amount Modified On : Nov 6, 2013 9:36 AM', 0, NULL, NULL),
(1089, 70, 59, '2013-11-05 21:38:58', 'The Lead "Testing Application" is Successfully Moved to Project.', 0, NULL, NULL),
(1088, 70, 59, '2013-11-05 21:38:19', 'Actual Worth Amount Modified On : Nov 5, 2013 9:38 PM', 0, NULL, NULL),
(1083, 102, 59, '2013-11-05 21:05:41', 'Actual Worth Amount Modified On : Nov 5, 2013 9:06 PM', 0, NULL, NULL),
(1084, 102, 59, '2013-11-05 21:06:05', 'Lead has been Re-assigned to: Sriram S<br />For Lead No.00102 ', 0, NULL, NULL),
(1085, 102, 59, '2013-11-05 21:05:54', 'Actual Worth Amount Modified On : Nov 5, 2013 9:06 PM', 0, NULL, NULL),
(1086, 102, 59, '2013-11-05 21:06:18', 'Lead Owner has been Re-assigned to: Sriram S<br />For Lead No.00102 ', 0, NULL, NULL),
(1087, 102, 59, '2013-11-05 21:19:36', 'Status Changed to: Prospect Sucessfully for the Lead - test lead for testing ', 0, NULL, NULL),
(1094, 102, 59, '2013-11-06 09:53:28', 'Morbi rutrum lobortis erat fringilla fringilla. In ultricies risus felis, id interdum nibh venenatis sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec, vulputate in tellus. Aenean et mollis eros. Praesent ullamcorper neque libero. <br /><br />This log has been emailed to:<br />Sriram S', 1, NULL, NULL),
(1095, 102, 59, '2013-11-06 09:53:44', 'Actual Worth Amount Modified On : Nov 6, 2013 9:54 AM', 0, NULL, NULL),
(1096, 102, 59, '2013-11-06 09:54:08', 'Lead Owner has been Re-assigned to: Ganesh Kum R<br />For Lead No.00102 ', 0, NULL, NULL),
(1097, 102, 59, '2013-11-06 10:02:32', 'Aliquam viverra lectus consequat, iaculis eros sed, molestie tortor. Phasellus et turpis ligula. Sed venenatis risus at bibendum semper. Duis gravida, turpis vel semper bibendum, ligula est blandit odio, at tempor massa nulla sit amet tellus. Donec volutpat, sem id vestibulum bibendum, libero justo blandit nibh, sit amet ultricies quam magna a nunc. by sri<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1098, 102, 59, '2013-11-06 10:02:44', 'Actual Worth Amount Modified On : Nov 6, 2013 10:03 AM', 0, NULL, NULL),
(1099, 102, 59, '2013-11-06 10:03:08', 'Lead Owner has been Re-assigned to: Sriram S<br />For Lead No.nteger ultrices bibendum gravida.&#8230; ', 0, NULL, NULL),
(1100, 102, 59, '2013-11-06 10:07:27', 'Status Changed to: Demo Scheduled Sucessfully for the Lead - nteger ultrices bibendum gravida.&#8230; ', 0, NULL, NULL),
(1101, 56, 59, '2013-11-06 13:50:42', 'Vivamus dapibus lacus bibendum tincidunt malesuada. Cras sagittis mauris interdum sem suscipit porta. Sed tellus elit, volutpat eget pellentesque at, porta nec velit. Duis sit amet ligula mauris. Aliquam erat volutpat. Aliquam molestie mauris at augue consectetur semper eget non lorem. Nulla ac massa et leo molestie consectetur. Maecenas sollicitudin mauris ut libero blandit dignissim. Integer a nibh pretium, cursus nunc sed, laoreet ipsum.<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1102, 56, 59, '2013-11-06 14:00:32', 'Hanunb byanay rihg gaht lkck kjiuh jdioholsadjfl k hdlkd  idhl  kldjdl njslk vivamins minste ckif kfrchf <br /><br />This log has been emailed to:<br />dinesh65@gmail.com, raamsri14@gmail.com', 0, NULL, NULL),
(1103, 56, 59, '2013-11-06 14:35:59', 'test ramji project<br /><br />This log has been emailed to:<br />Sriram S', 0, NULL, NULL),
(1104, 101, 59, '2013-11-06 14:48:54', 'Actual Worth Amount Modified On : Nov 6, 2013 2:49 PM', 0, NULL, NULL),
(1105, 82, 59, '2013-11-06 15:16:59', 'Lead Owner has been Re-assigned to: Sriram S<br />For Lead ta sdfas asdf asdf ', 0, NULL, NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=307 ;

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
(9, 1, 84, 1, 1, 1, 1),
(10, 2, 51, 1, 1, 1, 1),
(11, 2, 110, 1, 1, 1, 1),
(12, 2, 113, 0, 0, 0, 0),
(13, 2, 89, 1, 1, 1, 1),
(14, 2, 92, 1, 1, 1, 1),
(15, 2, 101, 1, 1, 1, 1),
(16, 2, 108, 1, 1, 1, 1),
(17, 2, 109, 1, 1, 1, 1),
(18, 2, 84, 1, 1, 1, 1),
(117, 3, 113, 1, 1, 1, 1),
(116, 3, 110, 1, 1, 1, 0),
(115, 3, 109, 0, 0, 0, 0),
(114, 3, 108, 1, 1, 1, 0),
(113, 3, 101, 1, 0, 1, 0),
(112, 3, 92, 1, 0, 0, 0),
(111, 3, 89, 1, 1, 1, 1),
(110, 3, 84, 1, 1, 1, 0),
(109, 3, 51, 1, 0, 1, 0),
(28, 4, 51, 1, 1, 1, 0),
(29, 4, 89, 1, 1, 1, 1),
(30, 4, 113, 0, 0, 0, 0),
(31, 4, 101, 0, 0, 0, 0),
(32, 4, 110, 0, 0, 0, 0),
(33, 4, 109, 0, 0, 0, 0),
(34, 4, 92, 0, 0, 0, 0),
(35, 4, 108, 1, 1, 1, 1),
(36, 4, 84, 1, 1, 1, 1),
(306, 8, 113, 0, 0, 0, 0),
(305, 8, 110, 0, 0, 0, 0),
(304, 8, 109, 0, 0, 0, 0),
(303, 8, 108, 1, 1, 1, 1),
(302, 8, 101, 0, 0, 0, 0),
(301, 8, 92, 0, 0, 0, 0),
(300, 8, 89, 1, 1, 1, 1),
(299, 8, 84, 1, 0, 0, 0),
(298, 8, 51, 1, 1, 0, 0),
(243, 9, 113, 0, 0, 0, 0),
(242, 9, 110, 0, 0, 0, 0),
(241, 9, 109, 0, 0, 0, 0),
(240, 9, 108, 1, 1, 1, 1),
(239, 9, 101, 0, 0, 0, 0),
(238, 9, 92, 0, 0, 0, 0),
(237, 9, 89, 1, 1, 1, 0),
(236, 9, 84, 1, 1, 1, 1),
(235, 9, 51, 1, 1, 1, 0),
(55, 14, 51, 1, 1, 1, 1),
(56, 14, 84, 1, 1, 1, 1),
(57, 14, 89, 1, 1, 1, 1),
(58, 14, 92, 0, 0, 0, 0),
(59, 14, 101, 0, 0, 0, 0),
(60, 14, 108, 0, 0, 0, 0),
(61, 14, 109, 0, 0, 0, 0),
(62, 14, 110, 1, 1, 1, 1),
(63, 14, 113, 0, 0, 0, 0),
(64, 16, 51, 1, 1, 1, 1),
(65, 16, 110, 0, 0, 0, 0),
(66, 16, 113, 0, 0, 0, 0),
(67, 16, 109, 0, 0, 0, 0),
(68, 16, 108, 1, 1, 1, 1),
(69, 16, 101, 1, 1, 1, 1),
(70, 16, 92, 0, 0, 0, 0),
(71, 16, 89, 1, 1, 1, 1),
(72, 16, 84, 0, 0, 0, 0),
(297, 17, 113, 0, 0, 0, 0),
(296, 17, 110, 1, 0, 0, 0),
(295, 17, 109, 0, 0, 0, 0),
(294, 17, 108, 1, 1, 1, 1),
(293, 17, 101, 0, 0, 0, 0),
(292, 17, 92, 0, 0, 0, 0),
(291, 17, 89, 1, 1, 1, 1),
(290, 17, 84, 1, 0, 1, 0),
(289, 17, 51, 0, 0, 0, 0),
(252, 18, 113, 0, 0, 0, 0),
(251, 18, 110, 0, 0, 0, 0),
(250, 18, 109, 0, 0, 0, 0),
(249, 18, 108, 0, 0, 0, 0),
(248, 18, 101, 0, 0, 0, 0),
(247, 18, 92, 0, 0, 0, 0),
(246, 18, 89, 1, 1, 1, 1),
(245, 18, 84, 1, 1, 0, 0),
(244, 18, 51, 1, 1, 1, 1),
(279, 11, 113, 1, 1, 1, 1),
(278, 11, 110, 1, 1, 1, 1),
(277, 11, 109, 0, 0, 0, 0),
(276, 11, 108, 1, 1, 1, 1),
(275, 11, 101, 1, 1, 1, 1),
(274, 11, 92, 0, 0, 0, 0),
(273, 11, 89, 1, 1, 1, 1),
(272, 11, 84, 1, 1, 1, 1),
(271, 11, 51, 1, 1, 1, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `crms_milestones`
--

INSERT INTO `crms_milestones` (`milestoneid`, `jobid_fk`, `milestone`, `due_date`, `status`, `position`) VALUES
(3, 34, 'Any Item', '2013-02-20 00:00:00', 0, 1),
(5, 43, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '2013-03-26 00:00:00', 0, 1),
(6, 43, 'Lorem Ipsum is simply dummy', '2013-03-26 00:00:00', 1, 2),
(7, 25, 'Status Changed to: Project Charter Approved. Convert to Projects In Progress Sucessfully on Lead No.00025 ', '2013-03-26 00:00:00', 0, 1),
(8, 46, 'Furnitures', '2013-03-27 00:00:00', 1, 1),
(15, 48, 'HH', '2013-04-17 00:00:00', 0, 1),
(16, 48, 'HH', '2013-04-23 00:00:00', 1, 2),
(17, 44, 'HH', '2013-04-24 00:00:00', 1, 1),
(19, 53, 'sadfadsf', '2013-04-11 00:00:00', 1, 1),
(21, 49, 'test', '2013-04-23 00:00:00', 1, 1),
(22, 49, 'test1', '2013-04-24 00:00:00', 1, 2),
(23, 54, 'test1', '2013-04-25 00:00:00', 1, 1),
(25, 90, 'dfasdf', '2013-08-31 00:00:00', 0, 1),
(31, 96, 'test', '2013-10-01 00:00:00', 1, 1),
(32, 96, 'test1', '2013-10-08 00:00:00', 1, 2),
(34, 82, 'rdxxtfxtf ', '2013-10-21 00:00:00', 0, 1),
(35, 82, 'asdf asdf asdf', '2013-10-28 00:00:00', 1, 2),
(37, 98, 'tewtwt ', '2013-10-30 00:00:00', 1, 1),
(38, 95, 'te4we rwer', '2013-10-30 00:00:00', 1, 1),
(39, 101, 'rerqwer qwer', '2013-11-25 00:00:00', 0, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `crms_package`
--

INSERT INTO `crms_package` (`package_id`, `package_name`, `package_price`, `typeid_fk`, `status`, `duration`, `details`) VALUES
(1, 'web package', '1200.00', 1, 'active', 1, 'test quotation'),
(2, 'web half package', '600.00', 2, 'active', 3, '');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `crms_package_type`
--

INSERT INTO `crms_package_type` (`type_id`, `package_name`, `type_months`, `package_flag`) VALUES
(1, 'First Package', '12', 'active'),
(2, 'second package', '6', 'active');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

--
-- Dumping data for table `crms_region`
--

INSERT INTO `crms_region` (`regionid`, `region_name`, `created_by`, `modified_by`, `created`, `modified`, `inactive`) VALUES
(1, 'Asia Pacific', 59, 59, '2013-01-22 17:50:06', '2013-11-18 18:09:22', 0),
(2, 'Europe', 59, 59, '2013-01-22 18:15:28', '2013-01-22 18:15:28', 0),
(3, 'North America', 59, 59, '2013-01-22 18:16:20', '2013-01-22 18:16:20', 0),
(4, 'America', 59, 59, '2013-01-29 15:09:49', '2013-11-18 19:23:30', 1),
(8, 'Asia', 59, 59, '2013-02-01 09:42:58', '2013-11-19 17:06:10', 0),
(11, 'Antartica2', 59, 59, '2013-02-05 11:29:05', '2013-02-19 14:53:51', 0),
(35, 'pre region6', 59, 59, '2013-02-18 18:24:00', '2013-02-18 18:24:00', 0),
(14, 'test region', 59, 59, '2013-02-05 15:43:34', '2013-11-19 12:15:21', 1),
(30, 'CRM Region', 59, 59, '2013-02-15 23:41:55', '2013-02-15 23:41:55', 0),
(17, 'pre region', 59, 59, '2013-02-14 18:15:31', '2013-02-14 18:15:31', 0),
(38, 'Africa', 59, 59, '2013-02-19 12:38:38', '2013-11-18 17:44:25', 0),
(43, 'Africa2', 59, 59, '2013-02-20 11:45:18', '2013-11-18 19:17:49', 1),
(44, 'Africa5', 59, 59, '2013-02-20 15:52:24', '2013-02-20 15:52:24', 0),
(22, 'pre region3', 59, 59, '2013-02-15 11:00:49', '2013-02-15 11:00:49', 0),
(24, 'pre region4', 59, 59, '2013-02-15 16:22:11', '2013-02-15 16:22:11', 0),
(25, 'pre region5', 59, 59, '2013-02-15 16:24:54', '2013-02-15 16:24:54', 0),
(31, 'North Region', 59, 59, '2013-02-16 00:04:53', '2013-02-16 00:04:53', 0),
(27, 'West Region', 59, 59, '2013-02-15 17:08:28', '2013-02-15 17:08:28', 0),
(29, 'South Region', 59, 59, '2013-02-15 20:54:25', '2013-02-15 20:54:25', 0),
(40, 'test region 4', 59, 59, '2013-02-19 12:49:04', '2013-02-19 12:49:04', 0),
(41, 'test region 5', 59, 59, '2013-02-19 12:49:36', '2013-10-07 15:14:28', 1),
(45, 'South America', 59, 59, '2013-03-13 16:35:33', '2013-03-13 16:35:33', 0),
(46, 'South Africa', 59, 59, '2013-03-27 15:20:10', '2013-03-27 15:20:10', 0),
(47, 'rrrrrrr', 59, 59, '2013-11-19 16:47:03', '2013-11-19 16:47:56', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `crms_roles`
--

INSERT INTO `crms_roles` (`id`, `name`, `created`, `modified`, `created_by`, `modified_by`, `inactive`) VALUES
(1, 'Administrator', '0000-00-00 00:00:00', '2013-07-24 11:30:23', 59, 59, 0),
(2, 'Management', '2012-12-11 17:19:28', '2013-07-08 15:09:53', 59, 59, 0),
(3, 'Project Manager', '2012-12-06 12:11:51', '2013-11-06 09:32:26', 59, 59, 0),
(4, 'Finance', '2012-12-06 12:12:43', '2013-01-02 05:21:11', 59, 59, 0),
(8, 'Developer', '2012-12-24 10:24:15', '2013-11-18 12:23:49', 59, 59, 0),
(9, 'QA', '2013-01-29 17:12:23', '2013-11-18 11:57:52', 59, 59, 1),
(11, 'Testing 1', '2013-02-01 10:00:47', '2013-11-18 12:02:39', 59, 59, 0),
(12, 'Testing 2', '2013-02-01 10:01:21', '2013-07-09 15:05:59', 59, 59, 0),
(14, 'Sales', '2013-02-15 16:28:38', '2013-07-09 14:36:39', 59, 59, 0),
(16, 'HH Role', '2013-02-22 12:27:02', '2013-05-06 11:26:37', 59, 59, 0),
(17, 'HH Team Role', '2013-02-25 12:00:17', '2013-11-18 12:03:19', 59, 59, 0),
(18, 'Quality Analyst', '2013-03-27 15:23:44', '2013-11-18 12:01:04', 59, 59, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=167 ;

--
-- Dumping data for table `crms_state`
--

INSERT INTO `crms_state` (`stateid`, `state_name`, `countryid`, `created`, `modified`, `created_by`, `modified_by`, `inactive`) VALUES
(1, 'Andra Pradesh', 15, '2013-01-22 17:56:39', '2013-01-22 17:56:39', 59, 59, 0),
(2, 'Arunachal Pradesh', 15, '2013-01-22 17:56:53', '2013-01-22 17:56:53', 59, 59, 0),
(3, 'Assam', 15, '2013-01-22 17:57:13', '2013-01-22 17:57:13', 59, 59, 0),
(4, 'Bihar', 15, '2013-01-22 17:57:25', '2013-11-19 11:46:31', 59, 59, 0),
(5, 'Chhattisgarh', 15, '2013-01-22 17:57:39', '2013-01-22 17:57:39', 59, 59, 0),
(6, 'Goa', 15, '2013-01-22 17:57:48', '2013-01-22 17:57:48', 59, 59, 0),
(7, 'Gujarat', 15, '2013-01-22 17:57:57', '2013-01-22 17:57:57', 59, 59, 0),
(8, 'Haryana', 15, '2013-01-22 17:58:40', '2013-01-22 17:58:40', 59, 59, 0),
(9, 'Himachal Pradesh', 15, '2013-01-22 17:58:50', '2013-01-22 17:58:50', 59, 59, 0),
(10, 'Jammu and Kashmir', 15, '2013-01-22 17:58:59', '2013-01-22 17:58:59', 59, 59, 0),
(11, 'Jharkhand', 15, '2013-01-22 17:59:07', '2013-01-22 17:59:07', 59, 59, 0),
(12, 'Karnataka', 15, '2013-01-22 17:59:13', '2013-01-22 17:59:13', 59, 59, 0),
(13, 'Kerala', 15, '2013-01-22 17:59:22', '2013-01-22 17:59:22', 59, 59, 0),
(14, 'Madya Pradesh', 15, '2013-01-22 17:59:32', '2013-01-22 17:59:32', 59, 59, 0),
(15, 'Maharashtra', 15, '2013-01-22 17:59:39', '2013-01-22 17:59:39', 59, 59, 0),
(16, 'Manipur', 15, '2013-01-22 17:59:46', '2013-01-22 17:59:46', 59, 59, 0),
(17, 'Meghalaya', 15, '2013-01-22 17:59:53', '2013-01-22 17:59:53', 59, 59, 0),
(18, 'Mizoram', 15, '2013-01-22 17:59:59', '2013-01-22 17:59:59', 59, 59, 0),
(19, 'Nagaland', 15, '2013-01-22 18:00:06', '2013-01-22 18:00:06', 59, 59, 0),
(20, 'Orissa', 15, '2013-01-22 18:00:14', '2013-01-22 18:00:14', 59, 59, 0),
(21, 'Punjab', 15, '2013-01-22 18:00:20', '2013-01-22 18:00:20', 59, 59, 0),
(22, 'Rajasthan', 15, '2013-01-22 18:00:27', '2013-01-22 18:00:27', 59, 59, 0),
(23, 'Sikkim', 15, '2013-01-22 18:00:35', '2013-01-22 18:00:35', 59, 59, 0),
(24, 'Tamil Nadu', 15, '2013-01-22 18:00:42', '2013-11-19 11:21:05', 59, 59, 0),
(25, 'Tripura', 15, '2013-01-22 18:00:50', '2013-01-22 18:00:50', 59, 59, 0),
(26, 'Uttaranchal', 15, '2013-01-22 18:00:57', '2013-01-22 18:00:57', 59, 59, 0),
(27, 'Uttar Pradesh', 15, '2013-01-22 18:01:06', '2013-01-22 18:01:06', 59, 59, 0),
(28, 'West Bengal', 15, '2013-01-22 18:01:15', '2013-01-22 18:01:15', 59, 59, 0),
(29, 'Andaman and Nicobar Islands', 15, '2013-01-22 18:05:05', '2013-11-05 14:27:57', 59, 59, 0),
(30, 'Chandigarh', 15, '2013-01-22 18:05:27', '2013-01-22 18:05:27', 59, 59, 0),
(31, 'Dadar and Nagar Haveli', 15, '2013-01-22 18:05:34', '2013-01-22 18:05:34', 59, 59, 0),
(32, 'Daman and Diu', 15, '2013-01-22 18:06:35', '2013-01-22 18:06:35', 59, 59, 0),
(33, 'Delhi', 15, '2013-01-22 18:06:41', '2013-01-22 18:06:41', 59, 59, 0),
(34, 'Lakshadeep', 15, '2013-01-22 18:06:47', '2013-01-22 18:06:47', 59, 59, 0),
(35, 'Pondicherry', 15, '2013-01-22 18:06:54', '2013-01-22 18:06:54', 59, 59, 0),
(36, 'Ashmore and Cartier Islands', 18, '2013-01-22 18:10:46', '2013-01-22 18:10:46', 59, 59, 0),
(37, 'Australian Antarctic Territory', 18, '2013-01-22 18:11:08', '2013-01-22 18:11:08', 59, 59, 0),
(38, 'Australian Capital Territory', 18, '2013-01-22 18:11:33', '2013-01-22 18:11:33', 59, 59, 0),
(39, 'Christmas Island', 18, '2013-01-22 18:11:46', '2013-01-22 18:11:46', 59, 59, 0),
(40, 'Cocos (Keeling) Islands', 18, '2013-01-22 18:11:56', '2013-01-22 18:11:56', 59, 59, 0),
(41, 'Coral Sea Islands', 18, '2013-01-22 18:12:08', '2013-01-22 18:12:08', 59, 59, 0),
(42, 'Heard Island and McDonald Islands', 18, '2013-01-22 18:12:19', '2013-01-22 18:12:19', 59, 59, 0),
(43, 'Jervis Bay Territory', 18, '2013-01-22 18:12:29', '2013-01-22 18:12:29', 59, 59, 0),
(44, 'New South Wales', 15, '2013-01-22 18:12:39', '2013-01-22 18:12:39', 59, 59, 0),
(45, 'Norfolk Island', 18, '2013-01-22 18:12:54', '2013-01-22 18:12:54', 59, 59, 0),
(46, 'Northern Territory', 18, '2013-01-22 18:13:06', '2013-01-22 18:13:36', 59, 59, 0),
(47, 'Queensland', 18, '2013-01-22 18:13:45', '2013-01-22 18:13:45', 59, 59, 0),
(48, 'South Australia', 18, '2013-01-22 18:13:57', '2013-01-22 18:13:57', 59, 59, 0),
(49, 'Tasmania', 18, '2013-01-22 18:14:05', '2013-01-22 18:14:05', 59, 59, 0),
(50, 'Victoria', 18, '2013-01-22 18:14:14', '2013-01-22 18:14:14', 59, 59, 0),
(51, 'Western Australia', 18, '2013-01-22 18:14:57', '2013-01-22 18:14:57', 59, 59, 0),
(52, 'Alabama', 23, '2013-01-22 18:17:15', '2013-11-19 11:31:54', 59, 59, 0),
(53, 'Alaska', 23, '2013-01-22 18:17:25', '2013-11-05 14:27:39', 59, 59, 0),
(54, 'Arizona', 23, '2013-01-22 18:17:39', '2013-01-22 18:17:39', 59, 59, 0),
(55, 'Arkansas', 23, '2013-01-22 18:17:47', '2013-01-22 18:17:47', 59, 59, 0),
(56, 'California', 23, '2013-01-22 18:17:55', '2013-01-22 18:17:55', 59, 59, 0),
(57, 'Colorado', 23, '2013-01-22 18:18:02', '2013-01-22 18:18:02', 59, 59, 0),
(58, 'Connecticut', 23, '2013-01-22 18:18:15', '2013-01-22 18:18:15', 59, 59, 0),
(59, 'Delaware', 23, '2013-01-22 18:18:35', '2013-01-22 18:18:35', 59, 59, 0),
(60, 'District Of Columbia', 23, '2013-01-22 18:18:43', '2013-01-22 18:18:43', 59, 59, 0),
(61, 'Florida', 23, '2013-01-22 18:18:51', '2013-01-22 18:18:51', 59, 59, 0),
(62, 'Georgia', 23, '2013-01-22 18:18:59', '2013-01-22 18:18:59', 59, 59, 0),
(63, 'Hawaii', 23, '2013-01-22 18:19:07', '2013-01-22 18:19:07', 59, 59, 0),
(64, 'Idaho', 23, '2013-01-22 18:19:14', '2013-01-22 18:19:14', 59, 59, 0),
(65, 'Illinois', 23, '2013-01-22 18:19:21', '2013-01-22 18:19:21', 59, 59, 0),
(66, 'Indiana', 23, '2013-01-22 18:19:28', '2013-01-22 18:19:28', 59, 59, 0),
(67, 'Iowa', 23, '2013-01-22 18:19:35', '2013-01-22 18:19:35', 59, 59, 0),
(68, 'Kansas', 23, '2013-01-22 18:19:41', '2013-01-22 18:19:41', 59, 59, 0),
(69, 'Kentucky', 23, '2013-01-22 18:19:49', '2013-01-22 18:19:49', 59, 59, 0),
(70, 'Louisiana', 23, '2013-01-22 18:20:19', '2013-01-22 18:20:19', 59, 59, 0),
(71, 'Maine', 23, '2013-01-22 18:20:26', '2013-01-22 18:20:26', 59, 59, 0),
(72, 'Maryland', 23, '2013-01-22 18:20:34', '2013-01-22 18:20:34', 59, 59, 0),
(73, 'Massachusetts', 23, '2013-01-22 18:20:42', '2013-01-22 18:20:42', 59, 59, 0),
(74, 'Michigan', 23, '2013-01-22 18:20:47', '2013-01-22 18:20:47', 59, 59, 0),
(75, 'Minnesota', 23, '2013-01-22 18:20:53', '2013-03-06 17:58:03', 59, 59, 0),
(76, 'Mississippi', 23, '2013-01-22 18:20:59', '2013-01-22 18:20:59', 59, 59, 0),
(77, 'Missouri', 23, '2013-01-22 18:21:05', '2013-01-22 18:21:05', 59, 59, 0),
(78, 'Montana', 23, '2013-01-22 18:21:12', '2013-01-22 18:21:12', 59, 59, 0),
(79, 'Nebraska', 23, '2013-01-22 18:21:20', '2013-11-19 11:31:35', 59, 59, 0),
(80, 'Nevada', 23, '2013-01-22 18:21:27', '2013-01-22 18:21:27', 59, 59, 0),
(81, 'New Hampshire', 23, '2013-01-22 18:21:33', '2013-01-22 18:21:33', 59, 59, 0),
(82, 'New Jersey', 23, '2013-01-22 18:21:41', '2013-01-22 18:21:41', 59, 59, 0),
(83, 'New Mexico', 23, '2013-01-22 18:21:50', '2013-01-22 18:21:50', 59, 59, 0),
(84, 'New York', 23, '2013-01-22 18:21:58', '2013-01-22 18:21:58', 59, 59, 0),
(85, 'North Carolina', 23, '2013-01-22 18:22:04', '2013-01-22 18:22:04', 59, 59, 0),
(86, 'North Dakota', 23, '2013-01-22 18:22:09', '2013-01-22 18:22:09', 59, 59, 0),
(87, 'Ohio', 23, '2013-01-22 18:22:16', '2013-01-22 18:22:16', 59, 59, 0),
(88, 'Oklahoma', 23, '2013-01-22 18:22:21', '2013-01-22 18:22:21', 59, 59, 0),
(89, 'Oregon', 23, '2013-01-22 18:22:29', '2013-01-22 18:22:29', 59, 59, 0),
(90, 'Pennsylvania', 23, '2013-01-22 18:22:35', '2013-01-22 18:22:35', 59, 59, 0),
(91, 'Rhode Island', 23, '2013-01-22 18:22:41', '2013-01-22 18:22:41', 59, 59, 0),
(92, 'South Carolina', 23, '2013-01-22 18:22:48', '2013-01-22 18:22:48', 59, 59, 0),
(93, 'South Dakota', 23, '2013-01-22 18:22:55', '2013-01-22 18:22:55', 59, 59, 0),
(94, 'Tennessee', 23, '2013-01-22 18:23:00', '2013-01-22 18:23:00', 59, 59, 0),
(95, 'Texas', 23, '2013-01-22 18:23:08', '2013-01-22 18:23:08', 59, 59, 0),
(96, 'Utah', 23, '2013-01-22 18:23:13', '2013-01-22 18:23:13', 59, 59, 0),
(97, 'Vermont', 23, '2013-01-22 18:23:18', '2013-03-06 17:56:49', 59, 59, 0),
(98, 'Virginia', 23, '2013-01-22 18:23:26', '2013-01-22 18:23:26', 59, 59, 0),
(99, 'Washington', 23, '2013-01-22 18:23:32', '2013-01-22 18:23:32', 59, 59, 0),
(100, 'West Virginia', 23, '2013-01-22 18:23:38', '2013-01-22 18:23:38', 59, 59, 0),
(101, 'Wisconsin', 23, '2013-01-22 18:23:43', '2013-01-22 18:23:43', 59, 59, 0),
(102, 'Wyoming', 23, '2013-01-22 18:23:48', '2013-01-22 18:23:48', 59, 59, 0),
(103, 'American Samoa', 23, '2013-01-22 18:24:53', '2013-01-22 18:24:53', 59, 59, 0),
(104, 'Guam', 23, '2013-01-22 18:25:10', '2013-01-22 18:25:10', 59, 59, 0),
(105, 'Northern Mariana Islands', 23, '2013-01-22 18:25:20', '2013-01-22 18:25:20', 59, 59, 0),
(106, 'Puerto Rico', 23, '2013-01-22 18:25:26', '2013-01-22 18:25:26', 59, 59, 0),
(107, 'Virgin Islands', 23, '2013-01-22 18:25:31', '2013-01-22 18:25:31', 59, 59, 0),
(108, 'Federated States Of Micronesia', 23, '2013-01-22 18:25:37', '2013-01-22 18:25:37', 59, 59, 0),
(109, 'Marshall Islands', 23, '2013-01-22 18:25:42', '2013-01-22 18:25:42', 59, 59, 0),
(110, 'Palau', 23, '2013-01-22 18:25:47', '2013-01-22 18:25:47', 59, 59, 0),
(111, 'Canal Zone', 23, '2013-01-22 18:25:53', '2013-01-22 18:25:53', 59, 59, 0),
(112, 'Philippine Islands', 23, '2013-01-22 18:25:58', '2013-01-22 18:25:58', 59, 59, 0),
(113, 'Singapore', 17, '2013-01-22 19:14:38', '2013-01-22 19:14:38', 59, 59, 0),
(114, 'Federal Territory', 16, '2013-01-22 19:22:36', '2013-01-22 19:25:09', 59, 59, 0),
(115, 'Castries', 24, '2013-01-29 15:25:20', '2013-11-19 11:31:14', 59, 59, 0),
(116, 'Beijing', 25, '2013-01-29 15:30:42', '2013-01-29 15:30:42', 59, 59, 0),
(117, 'Shenzhen', 25, '2013-01-29 15:31:09', '2013-01-29 15:31:09', 59, 59, 0),
(118, 'Tianjin', 25, '2013-01-29 15:31:21', '2013-01-29 15:31:21', 59, 59, 0),
(119, 'Dongguan', 25, '2013-01-29 15:31:33', '2013-01-29 15:31:33', 59, 59, 0),
(120, 'Hangzhou', 25, '2013-01-29 15:31:44', '2013-01-29 15:31:44', 59, 59, 0),
(121, 'Hong Kong', 25, '2013-01-29 15:31:56', '2013-01-29 15:31:56', 59, 59, 0),
(122, 'Wuhan', 25, '2013-01-29 15:32:09', '2013-01-29 15:32:09', 59, 59, 0),
(123, 'Test State', 27, '2013-01-29 17:36:50', '2013-01-29 17:37:37', 145, 145, 0),
(124, 'test state 2', 28, '2013-01-29 18:32:45', '2013-01-29 18:32:45', 145, 145, 0),
(125, 'Akita', 30, '2013-02-01 09:43:38', '2013-11-19 11:32:04', 59, 59, 0),
(126, 'Aomori', 30, '2013-02-01 09:48:41', '2013-02-01 09:48:41', 59, 59, 0),
(127, 'Chiba', 30, '2013-02-01 09:49:29', '2013-02-01 09:49:29', 59, 59, 0),
(128, 'Ehime', 30, '2013-02-01 09:49:43', '2013-02-01 09:49:43', 59, 59, 0),
(129, 'Fukui', 30, '2013-02-01 09:49:53', '2013-02-01 09:49:53', 59, 59, 0),
(130, 'Fukuoka', 30, '2013-02-01 09:50:01', '2013-02-01 09:50:01', 59, 59, 0),
(131, 'Fukushima', 30, '2013-02-01 09:50:19', '2013-02-01 09:50:19', 59, 59, 0),
(132, 'Gifu', 30, '2013-02-01 09:50:30', '2013-02-01 09:50:30', 59, 59, 0),
(134, 'Ibaraki', 30, '2013-02-01 09:54:14', '2013-02-01 09:54:14', 59, 59, 0),
(135, 'Ishikawa', 30, '2013-02-01 09:54:39', '2013-02-01 09:54:39', 59, 59, 0),
(136, 'Iwate', 30, '2013-02-01 09:54:50', '2013-02-01 09:54:50', 59, 59, 0),
(137, 'Antartica State', 32, '2013-02-05 11:33:54', '2013-02-05 11:33:54', 59, 59, 0),
(138, 'state test', 35, '2013-02-05 15:44:01', '2013-02-05 15:44:01', 59, 59, 1),
(139, 'test state1', 38, '2013-02-14 16:30:45', '2013-02-14 16:30:45', 59, 59, 1),
(140, 'Islamabad', 39, '2013-02-14 16:32:58', '2013-02-14 16:32:58', 59, 59, 0),
(141, 'Islamabad1', 39, '2013-02-14 16:32:58', '2013-02-14 16:32:58', 59, 59, 0),
(142, 'test s', 40, '2013-02-14 17:40:13', '2013-11-19 16:22:02', 59, 59, 1),
(146, 'South State', 44, '2013-02-15 20:47:06', '2013-02-15 20:47:06', 150, 150, 0),
(144, 'Tamil Nadu', 42, '2013-02-15 17:08:28', '2013-02-15 17:08:28', 59, 59, 0),
(145, 'State 65', 43, '2013-02-15 17:12:58', '2013-02-15 17:12:58', 59, 59, 0),
(147, 'Coimbatore', 15, '2013-02-20 11:17:14', '2013-02-20 11:17:14', 59, 59, 0),
(148, 'Madurai', 15, '2013-02-20 11:17:58', '2013-02-22 21:15:17', 59, 59, 0),
(150, 'Africaa', 47, '2013-02-20 15:57:16', '2013-11-19 16:21:17', 59, 59, 0),
(151, 'south america state', 48, '2013-03-13 16:36:45', '2013-03-13 16:36:45', 59, 59, 0),
(152, 'Amazonas', 50, '2013-03-27 15:21:54', '2013-03-27 15:21:54', 59, 59, 0),
(153, 'Tripura', 18, '2013-03-29 12:59:01', '2013-03-29 12:59:01', 161, 161, 0),
(154, 'France state', 20, '2013-04-24 14:43:57', '2013-04-24 14:43:57', 139, 139, 0),
(156, 'Harare', 53, '2013-05-06 16:51:45', '2013-05-06 16:51:45', 139, 139, 0),
(157, 'Midlands', 53, '2013-05-06 17:01:11', '2013-05-06 17:01:11', 139, 139, 0),
(158, 'Berlin', 22, '2013-05-06 17:16:46', '2013-05-06 17:16:46', 139, 139, 0),
(159, 'Dublin', 21, '2013-05-06 18:14:13', '2013-05-06 18:14:13', 139, 139, 0),
(160, 'Queenld', 18, '2013-11-05 10:17:04', '2013-11-05 10:17:04', 59, 59, 0),
(161, 'sss1-1', 55, '2013-11-19 16:49:06', '2013-11-19 18:03:12', 59, 59, 0),
(162, 'sss1-2', 55, '2013-11-19 18:03:30', '2013-11-19 18:03:30', 59, 59, 0),
(163, 'sss2-1', 56, '2013-11-19 18:03:45', '2013-11-19 18:03:45', 59, 59, 0),
(164, 'sss2-2', 56, '2013-11-19 18:04:06', '2013-11-19 18:04:06', 59, 59, 0),
(165, 'sss3-1', 57, '2013-11-19 18:04:20', '2013-11-19 18:04:20', 59, 59, 0),
(166, 'sss3-2', 57, '2013-11-19 18:04:33', '2013-11-19 18:04:33', 59, 59, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Dumping data for table `crms_tasks`
--

INSERT INTO `crms_tasks` (`taskid`, `jobid_fk`, `userid_fk`, `task`, `approved`, `status`, `is_complete`, `start_date`, `end_date`, `actualstart_date`, `actualend_date`, `created_by`, `hours`, `mins`, `created_on`, `marked_100pct`, `marked_complete`, `require_qc`, `priority`, `remarks`) VALUES
(2, 1, 118, 'Need to prepare two proposals.  \n\na)	The full fledged one with the additional 40 pages, and designed developed by enoah. b)	A scaled down version with 10 additional pages, and design, UI elements provided by Catapultas.\nRefer mail.', 1, 100, 1, '2013-01-24 00:00:00', '2013-01-24 00:00:00', '2013-01-24 00:00:00', '2013-01-24 20:00:45', 122, 0, 0, '2013-01-24 20:00:23', '2013-01-24 20:00:45', '2013-01-25 05:54:34', 0, 0, 'Proposal completed and uploaded to this crm system'),
(3, 3, 122, 'Send a profile and Proposal', 1, 100, 1, '2013-01-25 00:00:00', '2013-01-25 00:00:00', '0000-00-00 00:00:00', '2013-01-25 06:14:49', 122, 0, 0, '2013-01-25 06:14:27', '2013-01-25 06:14:49', '2013-01-25 06:15:01', 0, 0, 'Done have shared with client await response'),
(4, 5, 135, 'All email notification has got only internal server URL link.  Due to which not able to view the link from blackberrys.  Need to fix this issue.', 1, 100, 1, '2013-01-25 00:00:00', '2013-01-25 00:00:00', '2013-01-25 00:00:00', '2013-01-25 20:31:03', 118, 0, 0, '2013-01-25 20:19:42', '2013-01-25 20:31:03', '2013-01-25 21:40:54', 0, 0, ''),
(5, 5, 135, '1.  when a new task is assigned, the actual start date and actual end date should be blank.  Right now it assigns some date in the year 1970.  Fix this issue.\n2.  Remove the timings in planned end date field.  Right now it says 12 AM.', 1, 100, 1, '2013-01-25 00:00:00', '2013-01-25 00:00:00', '0000-00-00 00:00:00', '2013-01-25 20:12:47', 118, 0, 0, '2013-01-25 10:30:05', '2013-01-25 20:12:47', '2013-01-25 20:16:48', 0, 0, ''),
(6, 5, 135, '1.	Under create a lead, lead source please add a category as Partner.  \n2.	Under service requirement add eCommerce Portal, Recruitment Requirement, Contract Staffing, SAP Opportunity.\n3.	When I tried to add an item to a lead the system froze on me.  Please check.\n4.	When I added a file to a lead by default there was a jpeg file Ã¢â‚¬Å“ chrysanthemumÃ¢â‚¬Â please check on this.', 1, 100, 1, '2013-01-25 00:00:00', '2013-01-25 00:00:00', '0000-00-00 00:00:00', '2013-01-25 20:13:22', 118, 0, 0, '2013-01-25 10:32:24', '2013-01-25 20:13:22', '2013-01-25 20:17:32', 0, 0, ''),
(7, 5, 139, 'Please refer the document uploaded under files tab for the requirements and complete them.', 1, 0, 0, '2013-01-25 00:00:00', '2013-01-30 00:00:00', '1970-01-01 05:30:00', '0000-00-00 00:00:00', 118, 0, 0, '2013-01-28 11:58:50', NULL, NULL, 0, 0, ''),
(9, 6, 144, 'Need to come up with a functional spec for document management system to be used for econnect application. This will eventually be used instead of shared folders for maintaining files & folders.', 1, 0, 0, '2013-01-25 00:00:00', '2013-01-28 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 118, 0, 0, '2013-01-25 16:19:01', NULL, NULL, 0, 0, ''),
(10, 5, 135, 'sample task to check the date and time', 1, 100, 1, '2013-01-28 00:00:00', '2013-01-29 00:00:00', '2013-01-28 00:00:00', '2013-01-28 20:26:36', 118, 0, 0, '2013-01-28 20:26:22', '2013-01-28 20:26:36', '2013-01-28 20:27:36', 0, 0, ''),
(12, 8, 146, 'task for me', 1, 100, 1, '2013-01-29 00:00:00', '2013-01-30 00:00:00', '0000-00-00 00:00:00', '2013-01-29 18:25:00', 146, 0, 0, '2013-01-29 18:19:25', '2013-01-29 18:25:00', '2013-01-29 18:25:14', 0, 0, ''),
(16, 21, 147, 'eNoah task', 1, 0, 0, '2013-02-01 00:00:00', '2013-02-05 00:00:00', '2032-07-04 00:00:00', '0000-00-00 00:00:00', 145, 0, 0, '2013-02-15 17:20:24', NULL, NULL, 0, 0, 'dsds'),
(13, 9, 146, 'coding task', 1, 100, 1, '2013-01-29 00:00:00', '2013-01-29 00:00:00', '1999-11-30 00:00:00', '2013-01-29 19:01:28', 145, 0, 0, '2013-01-29 18:50:55', '2013-01-29 19:01:28', '2013-01-29 19:02:37', 0, 0, ''),
(14, 11, 145, 'testing task', 1, 80, 0, '2013-01-29 00:00:00', '2013-01-29 00:00:00', '2013-01-29 00:00:00', '0000-00-00 00:00:00', 146, 0, 0, '2013-01-29 19:08:44', NULL, NULL, 0, 0, ''),
(15, 13, 146, 'testing  task 2', 1, 0, 0, '2013-01-29 00:00:00', '2013-01-30 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 145, 0, 0, '2013-01-29 19:53:17', NULL, NULL, 0, 0, ''),
(17, 23, 145, 'CRM task', 1, 100, 0, '2013-02-01 00:00:00', '2013-02-01 00:00:00', '0000-00-00 00:00:00', '2013-02-01 16:39:58', 145, 0, 0, '2013-02-01 15:58:54', '2013-02-01 16:39:58', NULL, 0, 0, 'test task'),
(18, 26, 139, 'test test', 1, 100, 1, '2013-02-04 00:00:00', '2013-02-05 00:00:00', '2013-02-14 16:49:54', '2013-02-14 16:50:59', 139, 0, 0, '2013-02-14 16:49:06', '2013-02-14 16:50:59', '2013-02-14 16:51:13', 0, 0, 'teds'),
(24, 0, 139, 'test', 1, 0, 0, '2013-02-07 00:00:00', '2013-02-12 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-06 19:16:51', NULL, NULL, 0, 0, 'test'),
(23, 0, 149, 'New Task', 1, 100, 1, '2013-02-04 00:00:00', '2013-02-05 00:00:00', '2013-02-15 23:31:30', '2013-02-15 23:32:34', 59, 0, 0, '2013-02-05 11:03:32', '2013-02-15 23:32:34', '2013-02-20 13:59:11', 0, 0, 'Any remarks'),
(21, 0, 139, 'test', 1, 70, 0, '2013-02-04 00:00:00', '2013-02-05 00:00:00', '2013-02-15 23:33:52', '0000-00-00 00:00:00', 139, 0, 0, '2013-02-04 17:49:49', NULL, NULL, 0, 0, 'test'),
(25, 0, 139, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', 1, 0, 0, '2013-02-14 00:00:00', '2013-02-15 00:00:00', '2019-08-06 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-30 16:15:52', NULL, NULL, 0, 0, 'Please work on this. testing'),
(26, 12, 139, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', 1, 0, 0, '2013-02-14 00:00:00', '2013-02-21 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-02-14 18:32:53', NULL, NULL, 0, 0, ''),
(27, 32, 151, 'Any task description', 1, 100, 1, '2013-02-15 00:00:00', '2013-02-16 00:00:00', '2013-02-15 20:21:25', '2013-02-15 20:29:04', 150, 0, 0, '2013-02-15 18:52:34', '2013-02-15 20:29:04', '2013-02-15 20:29:44', 0, 0, 'Any remarks'),
(28, 0, 148, 'Any description', 1, 100, 1, '2013-02-15 00:00:00', '2013-02-16 00:00:00', '2013-02-15 20:39:23', '2013-02-15 20:39:59', 150, 0, 0, '2013-02-15 20:38:49', '2013-02-15 20:39:59', '2013-02-15 20:40:23', 0, 0, 'any other remarks'),
(29, 0, 148, 'any descrip 2', 1, 100, 1, '2013-02-15 00:00:00', '2013-02-16 00:00:00', '2013-02-15 20:32:21', '2013-02-15 20:32:48', 150, 0, 0, '2013-02-15 20:31:59', '2013-02-15 20:32:48', '2013-02-15 20:35:37', 0, 0, 'any other remarks'),
(30, 33, 148, 'Any Tasks', 1, 100, 1, '2013-02-20 00:00:00', '2013-02-21 00:00:00', '2013-02-20 12:30:04', '2013-02-20 16:15:15', 59, 0, 0, '2013-02-19 13:25:40', '2013-02-20 16:15:15', '2013-02-20 17:42:35', 0, 0, 'Any Remarks'),
(31, 0, 59, 'Any Test', 1, 100, 1, '2013-02-20 00:00:00', '2013-02-20 00:00:00', '0000-00-00 00:00:00', '2013-02-20 13:58:41', 148, 0, 0, '2013-02-20 12:33:35', '2013-02-20 13:58:41', '2013-02-20 14:10:28', 0, 0, 'Need to complete today'),
(32, 0, 148, 'New Task', 1, 100, 1, '2013-02-19 00:00:00', '2013-02-19 00:00:00', '2024-08-05 00:00:00', '2013-02-20 12:55:18', 59, 0, 0, '2013-02-20 12:56:13', '2013-02-20 12:55:18', '2013-02-20 13:58:18', 0, 0, 'Any Remarks'),
(33, 34, 59, 'Testing the eCRM', 1, 20, 1, '2013-02-20 00:00:00', '2013-02-21 00:00:00', '2013-02-20 15:49:08', '2013-02-20 15:31:56', 59, 0, 0, '2013-02-20 15:29:42', '2013-02-20 15:31:56', '2013-02-20 15:33:04', 0, 0, 'Testing the eCRM'),
(35, 35, 59, 'Any Test', 1, 20, 1, '2013-02-20 00:00:00', '2013-02-21 00:00:00', '2013-02-20 17:19:00', '2013-02-20 17:15:44', 59, 0, 0, '2013-02-20 17:09:53', '2013-02-20 17:15:44', '2013-02-20 17:10:25', 0, 0, 'Any Test'),
(36, 35, 59, 'Test', 1, 100, 1, '2013-02-20 00:00:00', '2013-02-20 00:00:00', '0000-00-00 00:00:00', '2013-02-20 17:15:28', 59, 0, 0, '2013-02-20 17:14:59', '2013-02-20 17:15:28', '2013-02-20 17:16:06', 0, 0, 'Test'),
(37, 0, 155, 'testing mail notification', 1, 0, 0, '2013-02-27 00:00:00', '2013-02-27 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-02-27 10:20:24', NULL, NULL, 0, 0, ''),
(38, 0, 155, 'assigning self task', 1, 0, 0, '2013-02-27 00:00:00', '2013-02-27 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 155, 0, 0, '2013-02-27 10:25:32', NULL, NULL, 0, 0, ''),
(39, 43, 139, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 1, 0, 0, '2013-03-19 00:00:00', '2013-03-27 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-03-19 19:14:20', NULL, NULL, 0, 0, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry'),
(40, 25, 139, 'Status Changed to: Project Charter Approved. Convert to Projects In Progress ', 1, 0, 0, '2013-03-19 00:00:00', '2013-03-27 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 156, 0, 0, '2013-03-19 20:28:04', NULL, NULL, 0, 0, ''),
(41, 0, 139, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.', 1, 10, 0, '2013-03-19 00:00:00', '2013-03-21 00:00:00', '2013-03-19 20:34:41', '0000-00-00 00:00:00', 59, 0, 0, '2013-03-19 20:33:54', NULL, NULL, 0, 0, ''),
(42, 46, 160, 'Furniture carving inspection', 1, 70, 0, '2013-03-27 00:00:00', '2013-03-31 00:00:00', '2013-03-27 16:42:01', '0000-00-00 00:00:00', 160, 0, 0, '2013-03-27 16:25:01', NULL, NULL, 0, 0, 'To be investigated on or before 31st of march'),
(43, 0, 148, 'Furniture design', 1, 0, 0, '2013-03-27 00:00:00', '2013-03-30 00:00:00', '2013-03-27 00:00:00', '0000-00-00 00:00:00', 160, 0, 0, '2013-03-27 16:28:02', NULL, NULL, 0, 0, 'End date March 30'),
(44, 47, 147, 'GPRS ', 1, 0, 0, '2013-03-27 00:00:00', '2013-03-28 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 160, 0, 0, '2013-03-27 16:47:25', NULL, NULL, 0, 0, 'GPRS'),
(45, 48, 161, 'Testing of HH Task', 1, 100, 1, '2013-04-02 00:00:00', '2013-04-03 00:00:00', '2013-04-01 00:00:00', '2013-04-01 12:21:43', 161, 0, 0, '2013-04-01 12:20:50', '2013-04-01 12:21:43', '2013-04-01 12:21:48', 0, 0, 'Any Contents'),
(46, 0, 139, 'Simple per-node content access module that performs a little differently than the majority of access modules. This module replaces the content teaser/body with "This post has been restricted to certain users" to show users that they are mis', 1, 0, 0, '2013-04-18 00:00:00', '2013-04-19 00:00:00', '1999-11-30 00:00:00', '0000-00-00 00:00:00', 139, 0, 0, '2013-04-11 15:40:04', NULL, NULL, 0, 0, ''),
(74, 101, 158, 'fgfmgnfgmffgmfgmfgm dfmdmdfmdmdmfd Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - test lead for testing', 1, 0, 0, '2013-11-05 00:00:00', '2013-11-08 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-04 17:48:05', NULL, NULL, 0, 0, 'fcgnfcgngggggg sdsfadf'),
(48, 0, 170, 'test', 1, 0, 0, '2013-07-10 00:00:00', '2013-07-11 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-07-08 17:24:08', NULL, NULL, 0, 0, 'tststst'),
(49, 0, 170, 'test task', 1, 0, 0, '2013-07-09 00:00:00', '2013-07-10 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 158, 0, 0, '2013-07-08 19:01:54', NULL, NULL, 0, 0, 'test'),
(50, 70, 147, 'test', 1, 0, 0, '2013-07-20 00:00:00', '2013-07-21 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 170, 0, 0, '2013-07-19 19:10:47', NULL, NULL, 0, 0, 'test'),
(51, 69, 163, 'test tasd', 1, 100, 1, '2013-07-23 00:00:00', '2013-07-26 00:00:00', '2013-11-05 00:00:00', '2013-11-06 18:39:31', 158, 0, 0, '2013-11-06 18:52:15', '2013-11-06 18:39:31', '2013-11-06 18:52:31', 0, 0, 'tststststst'),
(52, 0, 152, 'test task for lokesh babu', 1, 10, 0, '2013-08-09 00:00:00', '2013-08-13 00:00:00', '2013-08-09 11:13:39', '0000-00-00 00:00:00', 59, 0, 0, '2013-08-08 21:45:39', NULL, NULL, 0, 0, 'test task'),
(53, 0, 158, 'testr', 1, 100, 1, '2013-08-16 00:00:00', '2013-08-16 00:00:00', '2013-08-16 00:00:00', '2013-08-28 15:24:02', 59, 0, 0, '2013-08-16 12:45:00', '2013-08-28 15:24:02', '2013-08-28 15:24:25', 0, 0, 'asdf'),
(54, 0, 173, 'test task for shankar', 1, 40, 0, '2013-08-27 00:00:00', '2013-08-29 00:00:00', '2013-08-26 19:26:20', '0000-00-00 00:00:00', 59, 0, 0, '2013-08-26 19:25:06', NULL, NULL, 0, 0, 'sd fasdf asdf asdf'),
(55, 0, 139, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis facilisis justo. Aenean varius elit non velit sagittis posuere. Pellentesque ultricies eleifend lectus quis consectetur. Sed tristique eros vitae leo laoreet vulputate. Nullam ullamcorper id elit id semper. Donec ac rhoncus arcu. Proin viverra diam ac suscipit ultricies. Morbi malesuada leo risus, eget tempus neque posuere bibendum. Duis malesuada ante ac rhoncus tincidunt. Praesent enim nulla, dignissim in mollis id, dignissim sit amet dolor.', 1, 0, 0, '2013-08-27 00:00:00', '2013-08-29 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-30 16:44:52', NULL, NULL, 0, 0, 'asdfasdf'),
(56, 0, 152, 'Praesent eu sapien quis eros rhoncus facilisis. Aliquam semper sit amet lectus sed mattis. Duis sed tincidunt nibh, ut fringilla ipsum. Praesent adipiscing consectetur mauris, sed placerat orci tincidunt in. Nam et lacus odio. In posuere, metus vel posuere dictum, diam nulla pharetra dui, sit amet condimentum turpis metus ut massa. Etiam facilisis, enim eget condimentum malesuada, lacus quam dignissim nisi, ultricies facilisis felis libero vel ligula. Phasellus eleifend dapibus dignissim. ', 1, 0, 0, '2013-08-28 00:00:00', '2013-09-02 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-08-26 19:09:31', NULL, NULL, 0, 0, 'tstststs s st s ts s s s asdf asdf asdf asdf asdf'),
(57, 90, 173, 'res', 1, 100, 1, '2013-08-30 00:00:00', '2013-09-03 00:00:00', '2013-08-29 00:00:00', '2013-08-29 11:53:10', 59, 0, 0, '2013-08-29 11:52:59', '2013-08-29 11:53:10', '2013-08-29 11:54:06', 0, 0, 'asd fas df'),
(58, 0, 158, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis facilisis justo. Aenean varius elit non velit sagittis posuere. Pellentesque ultricies eleifend lectus quis consectetur. Sed tristique eros vitae sriram', 1, 20, 0, '2013-09-23 00:00:00', '2013-10-04 00:00:00', '2013-09-23 00:00:00', '0000-00-00 00:00:00', 168, 0, 0, '2013-09-30 15:40:23', NULL, NULL, 0, 0, 'test'),
(59, 0, 152, 'Test task need to be finish on next week.', 1, 0, 0, '2013-09-30 00:00:00', '2013-10-08 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 158, 0, 0, '2013-09-30 15:53:39', NULL, NULL, 0, 0, 'tttttt'),
(60, 74, 173, 'Plz work on revised proposal.', 1, 0, 0, '2013-09-30 00:00:00', '2013-10-03 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 172, 0, 0, '2013-09-30 19:32:35', NULL, NULL, 0, 0, 'test'),
(66, 93, 173, 'sdfasdfasdf', 1, 0, 0, '2013-10-25 00:00:00', '2013-10-30 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-24 14:39:12', NULL, NULL, 0, 0, 'adsfasd asdf'),
(67, 95, 173, 'yryeyw er sdfg sdfg', 1, 0, 0, '2013-10-21 00:00:00', '2013-10-29 00:00:00', '2013-10-29 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-31 18:48:53', NULL, NULL, 0, 0, 'yreye rt'),
(68, 100, 158, 'tesst wer qwer df asdf asdf asdf asdf asdf asdf aerqw daf qwer asdf ', 1, 0, 0, '2013-10-30 00:00:00', '2013-10-31 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-29 18:05:00', NULL, NULL, 0, 0, 'test asdf asdf asdf'),
(69, 98, 158, 'terwe wqer qwe qwe rrq', 1, 0, 0, '2013-10-29 00:00:00', '2013-10-29 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-30 12:59:02', NULL, NULL, 0, 0, 'tewer wer qwer'),
(71, 98, 159, 'dfas dfa sdfa sdf asdf asdf', 1, 0, 0, '2013-10-31 00:00:00', '2013-10-31 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-10-30 19:04:20', NULL, NULL, 0, 0, 'as df asdf asdfasdf'),
(72, 61, 173, 'tewts fsd fasdfa', 1, 0, 0, '2013-11-05 00:00:00', '2013-11-18 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-18 15:31:47', NULL, NULL, 0, 0, 'tewt'),
(73, 101, 173, 'tewtw', 1, 0, 0, '2013-11-12 00:00:00', '2013-11-28 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-04 14:17:57', NULL, NULL, 0, 0, 'tewt'),
(75, 102, 158, 'sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,', 1, 100, 1, '2013-11-08 00:00:00', '2013-11-10 00:00:00', '0000-00-00 00:00:00', '2013-11-06 18:55:54', 163, 0, 0, '2013-11-06 18:06:17', '2013-11-06 18:55:54', '2013-11-06 18:56:53', 0, 0, 'test remarks test test df asdfasdfasdf'),
(76, 102, 163, 'Morbi rutrum lobortis erat fringilla fringilla. In ultricies risus felis, id interdum nibh venenatis sit amet. Duis et justo ultricies purus vehicula fermentum.', 1, 0, 0, '2013-11-07 00:00:00', '2013-11-07 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-06 18:12:20', NULL, NULL, 0, 0, 'plz finish by tomorrow'),
(77, 102, 172, 'Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing', 1, 0, 0, '2013-11-07 00:00:00', '2013-11-08 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 59, 0, 0, '2013-11-06 18:14:54', NULL, NULL, 0, 0, 'two days');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

--
-- Dumping data for table `crms_tasks_track`
--

INSERT INTO `crms_tasks_track` (`tasktrackid`, `taskid_fk`, `event`, `date`, `event_data`) VALUES
(1, 2, 'Task Update', '2013-01-24 20:00:23', '{"taskid":"2","jobid_fk":"1","userid_fk":"118","task":"Need to prepare two proposals.  \\n\\na)\\tThe full fledged one with the additional 40 pages, and designed developed by enoah. b)\\tA scaled down version with 10 additional pages, and design, UI elements provided by Catapultas.\\nRefer mail.","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-24 00:00:00","end_date":"2013-01-24 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"122","hours":"0","mins":"0","created_on":"2013-01-24 07:28:43","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(2, 4, 'Task Update', '2013-01-25 20:19:07', '{"taskid":"4","jobid_fk":"5","userid_fk":"134","task":"All email notification has got only internal server URL link.  Due to which not able to view the link from blackberrys.  Need to fix this issue.","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-25 00:00:00","end_date":"2013-01-25 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"118","hours":"0","mins":"0","created_on":"2013-01-25 10:27:36","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(3, 4, 'Task Update', '2013-01-25 20:19:42', '{"taskid":"4","jobid_fk":"5","userid_fk":"134","task":"All email notification has got only internal server URL link.  Due to which not able to view the link from blackberrys.  Need to fix this issue.","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-25 00:00:00","end_date":"2013-01-25 00:00:00","actualstart_date":"2013-01-25 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"118","hours":"0","mins":"0","created_on":"2013-01-25 20:19:07","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(4, 7, 'Task Update', '2013-01-28 11:58:50', '{"taskid":"7","jobid_fk":"5","userid_fk":"139","task":"Please refer the mail about the task module pending tasks to be completed and finish them in the development environment and inform me","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-25 00:00:00","end_date":"2013-01-30 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"118","hours":"0","mins":"0","created_on":"2013-01-25 11:06:19","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(5, 10, 'Task Update', '2013-01-28 20:26:22', '{"taskid":"10","jobid_fk":"5","userid_fk":"135","task":"sample task to check the date and time","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-28 00:00:00","end_date":"2013-01-29 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"118","hours":"0","mins":"0","created_on":"2013-01-28 20:25:28","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(6, 11, 'Task Update', '2013-01-29 18:14:12', '{"taskid":"11","jobid_fk":"8","userid_fk":"145","task":"test task1","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-31 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:06:48","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fdsf"}'),
(7, 11, 'Task Update', '2013-01-29 18:17:34', '{"taskid":"11","jobid_fk":"8","userid_fk":"146","task":"test task1","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-31 00:00:00","actualstart_date":"1999-11-30 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:14:12","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fdsf"}'),
(8, 11, 'Task Update', '2013-01-29 18:20:02', '{"taskid":"11","jobid_fk":"8","userid_fk":"145","task":"test task1","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-31 00:00:00","actualstart_date":"2036-04-21 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:17:34","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fdsf"}'),
(9, 11, 'Task Update', '2013-01-29 18:49:58', '{"taskid":"11","jobid_fk":"8","userid_fk":"146","task":"test task1","approved":"1","status":"70","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-31 00:00:00","actualstart_date":"2026-10-27 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:20:02","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fdsf"}'),
(10, 13, 'Task Update', '2013-01-29 18:50:55', '{"taskid":"13","jobid_fk":"9","userid_fk":"145","task":"coding task","approved":"1","status":"90","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-29 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:24:28","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(11, 11, 'Task Update', '2013-01-29 19:02:06', '{"taskid":"11","jobid_fk":"8","userid_fk":"145","task":"test task1","approved":"1","status":"70","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-31 00:00:00","actualstart_date":"2033-04-17 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-01-29 18:49:58","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fdsf"}'),
(12, 14, 'Task Update', '2013-01-29 19:08:44', '{"taskid":"14","jobid_fk":"11","userid_fk":"145","task":"testing task","approved":"1","status":"0","is_complete":"0","start_date":"2013-01-29 00:00:00","end_date":"2013-01-29 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"146","hours":"0","mins":"0","created_on":"2013-01-29 19:07:22","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(13, 16, 'Task Update', '2013-02-01 16:44:01', '{"taskid":"16","jobid_fk":"21","userid_fk":"147","task":"eNoah task","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-01 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-02-01 12:59:49","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"dsds"}'),
(14, 16, 'Task Update', '2013-02-01 17:57:58', '{"taskid":"16","jobid_fk":"21","userid_fk":"147","task":"eNoah task","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-01 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"2013-02-01 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-02-01 16:44:01","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"dsds"}'),
(15, 18, 'Task Update', '2013-02-04 15:41:34', '{"taskid":"18","jobid_fk":"26","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-04 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"139","hours":"0","mins":"0","created_on":"2013-02-04 15:41:15","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(16, 19, 'Task Update', '2013-02-04 15:45:46', '{"taskid":"19","jobid_fk":"26","userid_fk":"139","task":"test123","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-04 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"139","hours":"0","mins":"0","created_on":"2013-02-04 15:45:21","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(17, 18, 'Task Update', '2013-02-04 15:54:31', '{"taskid":"18","jobid_fk":"26","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-04 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"1999-11-30 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"139","hours":"0","mins":"0","created_on":"2013-02-04 15:41:34","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"teds"}'),
(18, 18, 'Task Update', '2013-02-14 16:49:06', '{"taskid":"18","jobid_fk":"26","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-04 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"2009-08-05 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"139","hours":"0","mins":"0","created_on":"2013-02-04 15:54:31","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"teds"}'),
(19, 25, 'Task Update', '2013-02-14 16:50:32', '{"taskid":"25","jobid_fk":"0","userid_fk":"139","task":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-14 00:00:00","end_date":"2013-02-15 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-14 16:26:23","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(20, 16, 'Task Update', '2013-02-15 17:19:51', '{"taskid":"16","jobid_fk":"21","userid_fk":"147","task":"eNoah task","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-01 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"2006-08-06 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-02-01 17:57:58","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"dsds"}'),
(21, 16, 'Task Update', '2013-02-15 17:20:24', '{"taskid":"16","jobid_fk":"21","userid_fk":"147","task":"eNoah task","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-01 00:00:00","end_date":"2013-02-05 00:00:00","actualstart_date":"2012-01-27 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"145","hours":"0","mins":"0","created_on":"2013-02-15 17:19:51","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"dsds"}'),
(22, 27, 'Task Update', '2013-02-15 18:33:42', '{"taskid":"27","jobid_fk":"32","userid_fk":"148","task":"Any task description","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-15 00:00:00","end_date":"2013-02-16 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"150","hours":"0","mins":"0","created_on":"2013-02-15 18:33:00","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any remarks"}'),
(23, 27, 'Task Update', '2013-02-15 18:34:04', '{"taskid":"27","jobid_fk":"32","userid_fk":"148","task":"Any task description","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-15 00:00:00","end_date":"2013-02-16 00:00:00","actualstart_date":"1999-11-30 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"150","hours":"0","mins":"0","created_on":"2013-02-15 18:33:42","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any remarks"}'),
(24, 27, 'Task Update', '2013-02-15 18:52:34', '{"taskid":"27","jobid_fk":"32","userid_fk":"148","task":"Any task description","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-15 00:00:00","end_date":"2013-02-16 00:00:00","actualstart_date":"2036-04-21 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"150","hours":"0","mins":"0","created_on":"2013-02-15 18:34:04","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any remarks"}'),
(25, 28, 'Task Update', '2013-02-15 20:38:49', '{"taskid":"28","jobid_fk":"0","userid_fk":"151","task":"Any description","approved":"1","status":"100","is_complete":"0","start_date":"2013-02-15 00:00:00","end_date":"2013-02-16 00:00:00","actualstart_date":"2013-02-15 20:33:07","actualend_date":"2013-02-15 20:37:00","created_by":"150","hours":"0","mins":"0","created_on":"2013-02-15 20:31:28","marked_100pct":"2013-02-15 20:37:00","marked_complete":null,"require_qc":"0","priority":"0","remarks":"any other remarks"}'),
(26, 32, 'Task Update', '2013-02-20 12:54:57', '{"taskid":"32","jobid_fk":"0","userid_fk":"148","task":"New Task","approved":"1","status":"100","is_complete":"0","start_date":"2013-02-19 00:00:00","end_date":"2013-02-19 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"2013-02-20 12:53:30","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-20 12:35:35","marked_100pct":"2013-02-20 12:53:30","marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any Remarks"}'),
(27, 32, 'Task Update', '2013-02-20 12:56:13', '{"taskid":"32","jobid_fk":"0","userid_fk":"148","task":"New Task","approved":"1","status":"100","is_complete":"0","start_date":"2013-02-19 00:00:00","end_date":"2013-02-19 00:00:00","actualstart_date":"2013-02-19 00:00:00","actualend_date":"2013-02-20 12:55:18","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-20 12:54:57","marked_100pct":"2013-02-20 12:55:18","marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any Remarks"}'),
(28, 25, 'Task Update', '2013-02-25 14:12:39', '{"taskid":"25","jobid_fk":"0","userid_fk":"139","task":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-14 00:00:00","end_date":"2013-02-15 00:00:00","actualstart_date":"2013-02-14 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-14 16:50:32","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(29, 43, 'Task Update', '2013-03-27 16:28:02', '{"taskid":"43","jobid_fk":"0","userid_fk":"148","task":"Furniture design","approved":"1","status":"0","is_complete":"0","start_date":"2013-03-27 00:00:00","end_date":"2013-03-30 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"160","hours":"0","mins":"0","created_on":"2013-03-27 16:27:47","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"End date March 30"}'),
(30, 45, 'Task Update', '2013-04-01 12:20:50', '{"taskid":"45","jobid_fk":"48","userid_fk":"161","task":"Testing","approved":"1","status":"20","is_complete":"0","start_date":"2013-04-02 00:00:00","end_date":"2013-04-03 00:00:00","actualstart_date":"2013-04-01 12:20:33","actualend_date":"0000-00-00 00:00:00","created_by":"161","hours":"0","mins":"0","created_on":"2013-04-01 12:19:19","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"Any Contents"}'),
(31, 46, 'Task Update', '2013-04-11 15:40:04', '{"taskid":"46","jobid_fk":"0","userid_fk":"139","task":"Simple per-node content access module that performs a little differently than the majority of access modules. This module replaces the content teaser\\/body with \\"This post has been restricted to certain users\\" to show users that they are mis","approved":"1","status":"0","is_complete":"0","start_date":"2013-04-18 00:00:00","end_date":"2013-04-19 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"139","hours":"0","mins":"0","created_on":"2013-04-11 15:39:54","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":""}'),
(32, 24, 'Task Update', '2013-04-15 18:02:26', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-07 17:14:03","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(33, 53, 'Task Update', '2013-08-16 12:06:45', '{"taskid":"53","jobid_fk":"0","userid_fk":"158","task":"testr","approved":"1","status":"0","is_complete":"0","start_date":"2013-08-17 00:00:00","end_date":"2013-08-20 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-16 12:04:01","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"asdf"}'),
(34, 53, 'Task Update', '2013-08-16 12:28:06', '{"taskid":"53","jobid_fk":"0","userid_fk":"158","task":"testr","approved":"1","status":"0","is_complete":"0","start_date":"2013-08-17 00:00:00","end_date":"2013-08-20 00:00:00","actualstart_date":"2013-08-16 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-16 12:06:45","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"asdf"}'),
(35, 53, 'Task Update', '2013-08-16 12:45:00', '{"taskid":"53","jobid_fk":"0","userid_fk":"158","task":"testr","approved":"1","status":"0","is_complete":"0","start_date":"2013-08-17 00:00:00","end_date":"2013-08-20 00:00:00","actualstart_date":"2013-08-17 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-16 12:28:06","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"asdf"}'),
(36, 54, 'Task Update', '2013-08-26 19:13:34', '{"taskid":"54","jobid_fk":"0","userid_fk":"173","task":"test task for shankar","approved":"1","status":"20","is_complete":"0","start_date":"2013-08-27 00:00:00","end_date":"2013-08-29 00:00:00","actualstart_date":"2013-08-26 19:12:25","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-26 18:45:09","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"terstststst"}'),
(37, 54, 'Task Update', '2013-08-26 19:19:48', '{"taskid":"54","jobid_fk":"0","userid_fk":"173","task":"test task for shankar","approved":"1","status":"20","is_complete":"0","start_date":"2013-08-27 00:00:00","end_date":"2013-08-29 00:00:00","actualstart_date":"2013-08-26 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-26 19:13:34","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"terststststfa sdf asdf"}'),
(38, 54, 'Task Update', '2013-08-26 19:25:06', '{"taskid":"54","jobid_fk":"0","userid_fk":"173","task":"test task for shankar","approved":"1","status":"20","is_complete":"0","start_date":"2013-08-27 00:00:00","end_date":"2013-08-29 00:00:00","actualstart_date":"2013-08-26 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-26 19:19:48","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"tsestrs ds asdf"}'),
(39, 57, 'Task Update', '2013-08-29 11:52:02', '{"taskid":"57","jobid_fk":"90","userid_fk":"161","task":"res","approved":"1","status":"0","is_complete":"0","start_date":"2013-08-30 00:00:00","end_date":"2013-09-03 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-29 11:50:44","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"tstst"}'),
(40, 57, 'Task Update', '2013-08-29 11:52:59', '{"taskid":"57","jobid_fk":"90","userid_fk":"173","task":"res","approved":"1","status":"10","is_complete":"0","start_date":"2013-08-30 00:00:00","end_date":"2013-09-03 00:00:00","actualstart_date":"2013-08-29 11:52:33","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-29 11:52:02","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"tstst"}'),
(41, 58, 'Task Update', '2013-09-23 16:27:06', '{"taskid":"58","jobid_fk":"0","userid_fk":"158","task":"test task for sriram","approved":"1","status":"10","is_complete":"0","start_date":"2013-09-23 00:00:00","end_date":"2013-09-24 00:00:00","actualstart_date":"2013-09-23 16:04:59","actualend_date":"0000-00-00 00:00:00","created_by":"168","hours":"0","mins":"0","created_on":"2013-09-23 16:02:47","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(42, 58, 'Task Update', '2013-09-30 15:40:23', '{"taskid":"58","jobid_fk":"0","userid_fk":"158","task":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis facilisis justo. Aenean varius elit non velit sagittis posuere. Pellentesque ultricies eleifend lectus quis consectetur. Sed tristique eros vitae sriram","approved":"1","status":"20","is_complete":"0","start_date":"2013-09-23 00:00:00","end_date":"2013-09-24 00:00:00","actualstart_date":"2013-09-23 17:15:13","actualend_date":"0000-00-00 00:00:00","created_by":"168","hours":"0","mins":"0","created_on":"2013-09-23 16:27:06","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(43, 59, 'Task Update', '2013-09-30 15:53:39', '{"taskid":"59","jobid_fk":"0","userid_fk":"161","task":"Test task need to be finish on next week.","approved":"1","status":"0","is_complete":"0","start_date":"2013-09-30 00:00:00","end_date":"2013-10-08 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"158","hours":"0","mins":"0","created_on":"2013-09-30 15:48:04","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"tttttt"}'),
(44, 24, 'Task Update', '2013-10-30 16:14:13', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-04-15 18:02:26","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(45, 24, 'Task Update', '2013-10-30 16:14:20', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-10-30 16:14:13","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(46, 25, 'Task Update', '2013-10-30 16:15:52', '{"taskid":"25","jobid_fk":"0","userid_fk":"139","task":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus non ipsum enim. Vivamus vitae mauris libero. Donec malesuada laoreet orci ut pharetra. Nam gravida lacus nec sapien sodales semper. Donec et accumsan sem. In hac habitasse platea dictumst. Nulla in leo non ligula dignissim auctor sit amet in eros.","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-14 00:00:00","end_date":"2013-02-15 00:00:00","actualstart_date":"2019-08-06 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-02-25 14:12:39","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"Please work on this.."}'),
(47, 24, 'Task Update', '2013-10-30 16:16:34', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-10-30 16:14:20","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(48, 47, 'Task Update', '2013-10-30 16:40:53', '{"taskid":"47","jobid_fk":"0","userid_fk":"168","task":"test task for testing","approved":"1","status":"0","is_complete":"0","start_date":"2013-07-09 00:00:00","end_date":"2013-07-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-07-08 17:22:04","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(49, 55, 'Task Update', '2013-10-30 16:44:52', '{"taskid":"55","jobid_fk":"0","userid_fk":"139","task":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis facilisis justo. Aenean varius elit non velit sagittis posuere. Pellentesque ultricies eleifend lectus quis consectetur. Sed tristique eros vitae leo laoreet vulputate. Nullam ullamcorper id elit id semper. Donec ac rhoncus arcu. Proin viverra diam ac suscipit ultricies. Morbi malesuada leo risus, eget tempus neque posuere bibendum. Duis malesuada ante ac rhoncus tincidunt. Praesent enim nulla, dignissim in mollis id, dignissim sit amet dolor. ","approved":"1","status":"0","is_complete":"0","start_date":"2013-08-27 00:00:00","end_date":"2013-08-29 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-08-26 19:06:11","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"asdfasdf"}'),
(50, 24, 'Task Update', '2013-10-30 18:06:45', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-10-30 16:16:34","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(51, 67, 'Task Update', '2013-10-31 18:48:53', '{"taskid":"67","jobid_fk":"95","userid_fk":"173","task":"yryeyw er sdfg sdfg","approved":"1","status":"0","is_complete":"0","start_date":"2013-10-21 00:00:00","end_date":"2013-10-29 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-10-28 18:28:45","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"yreye rt"}'),
(52, 24, 'Task Update', '2013-11-04 14:15:15', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-10-30 18:06:45","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(53, 74, 'Task Update', '2013-11-04 17:48:05', '{"taskid":"74","jobid_fk":"101","userid_fk":"158","task":"fgfmgnfgmffgmfgmfgm dfmdmdfmdmdmfd Status Changed to: Proposal Sent to client. Awaiting Approval Sucessfully for the Lead - test lead for testing ","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-05 00:00:00","end_date":"2013-11-08 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-04 17:10:31","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"fcgnfcgngggggg"}'),
(54, 75, 'Task Update', '2013-11-06 17:38:37', '{"taskid":"75","jobid_fk":"102","userid_fk":"158","task":"sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-08 00:00:00","end_date":"2013-11-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-06 15:23:02","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test remarks"}'),
(55, 75, 'Task Update', '2013-11-06 17:54:04', '{"taskid":"75","jobid_fk":"102","userid_fk":"158","task":"sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-08 00:00:00","end_date":"2013-11-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-06 17:38:37","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test remarks test"}'),
(56, 75, 'Task Update', '2013-11-06 17:54:30', '{"taskid":"75","jobid_fk":"102","userid_fk":"158","task":"sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-08 00:00:00","end_date":"2013-11-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-06 17:54:04","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test remarks test"}'),
(57, 75, 'Task Update', '2013-11-06 17:56:01', '{"taskid":"75","jobid_fk":"102","userid_fk":"158","task":"sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-08 00:00:00","end_date":"2013-11-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-06 17:54:30","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test remarks test test"}'),
(58, 75, 'Task Update', '2013-11-06 18:06:17', '{"taskid":"75","jobid_fk":"102","userid_fk":"158","task":"sit amet. Duis et justo ultricies purus vehicula fermentum. Suspendisse eros libero, consequat quis bibendum in, vulputate nec velit. Sed ligula tortor, mollis a adipiscing nec,","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-08 00:00:00","end_date":"2013-11-10 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"163","hours":"0","mins":"0","created_on":"2013-11-06 17:56:01","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test remarks test test tewt"}'),
(59, 51, 'Task Update', '2013-11-06 18:38:51', '{"taskid":"51","jobid_fk":"69","userid_fk":"163","task":"test tasd","approved":"1","status":"90","is_complete":"0","start_date":"2013-07-23 00:00:00","end_date":"2013-07-26 00:00:00","actualstart_date":"2013-11-06 18:38:12","actualend_date":"2013-11-06 18:36:32","created_by":"158","hours":"0","mins":"0","created_on":"2013-07-22 14:37:13","marked_100pct":"2013-11-06 18:36:32","marked_complete":null,"require_qc":"0","priority":"0","remarks":"tststststst"}'),
(60, 51, 'Task Update', '2013-11-06 18:39:14', '{"taskid":"51","jobid_fk":"69","userid_fk":"163","task":"test tasd","approved":"1","status":"90","is_complete":"0","start_date":"2013-07-23 00:00:00","end_date":"2013-07-26 00:00:00","actualstart_date":"2013-11-05 00:00:00","actualend_date":"2013-11-06 18:36:32","created_by":"158","hours":"0","mins":"0","created_on":"2013-11-06 18:38:51","marked_100pct":"2013-11-06 18:36:32","marked_complete":null,"require_qc":"0","priority":"0","remarks":"tststststst"}'),
(61, 51, 'Task Update', '2013-11-06 18:52:15', '{"taskid":"51","jobid_fk":"69","userid_fk":"163","task":"test tasd","approved":"1","status":"100","is_complete":"0","start_date":"2013-07-23 00:00:00","end_date":"2013-07-26 00:00:00","actualstart_date":"2013-11-05 00:00:00","actualend_date":"2013-11-06 18:39:31","created_by":"158","hours":"0","mins":"0","created_on":"2013-11-06 18:39:14","marked_100pct":"2013-11-06 18:39:31","marked_complete":null,"require_qc":"0","priority":"0","remarks":"tststststst"}'),
(62, 24, 'Task Update', '2013-11-06 19:16:51', '{"taskid":"24","jobid_fk":"0","userid_fk":"139","task":"test","approved":"1","status":"0","is_complete":"0","start_date":"2013-02-07 00:00:00","end_date":"2013-02-12 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-04 14:15:15","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"test"}'),
(63, 72, 'Task Update', '2013-11-18 15:31:47', '{"taskid":"72","jobid_fk":"61","userid_fk":"173","task":"tewts fsd fasdfa","approved":"1","status":"0","is_complete":"0","start_date":"2013-11-05 00:00:00","end_date":"2013-11-30 00:00:00","actualstart_date":"0000-00-00 00:00:00","actualend_date":"0000-00-00 00:00:00","created_by":"59","hours":"0","mins":"0","created_on":"2013-11-04 14:02:14","marked_100pct":null,"marked_complete":null,"require_qc":"0","priority":"0","remarks":"tewt"}');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=190 ;

--
-- Dumping data for table `crms_users`
--

INSERT INTO `crms_users` (`userid`, `role_id`, `first_name`, `last_name`, `password`, `email`, `add_email`, `use_both_emails`, `phone`, `mobile`, `level`, `is_pm`, `sales_code`, `start_date`, `signature`, `key`, `bldg_key`, `inactive`) VALUES
(59, 1, 'Admin', 'eNoah - iSolution', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'admin@enoahisolution.com', '0', 0, '', '9962673215', 1, 1, '0', NULL, 'eNoah - iSolution', 0, 0, 0),
(135, 8, 'Surendar', 'K', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'ksurendar@enoahisolution.com', NULL, 0, '', '', 1, 0, '0', NULL, NULL, 0, 0, 0),
(139, 3, 'Ramji', 'B', 'e66fb371820633295413ba57518722cbae18dcb3', 'ramji@enoahisolution.com', '0', 0, '', '', 2, 0, '0', NULL, 'Regards,\nRamji', 0, 0, 0),
(147, 12, 'Surya', 'M', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'msurya@enoahisolution.com', NULL, 0, '', '', 3, 0, '0', NULL, NULL, 0, 0, 0),
(149, 8, 'Nagendra', 'P', '41589fdd0f4220c50eab22259d45629b5bb0848f', 'pnagendra@enoahisolution.com', '0', 0, '12345678', '1234567890', 5, 0, '0', NULL, 'A', 0, 0, 0),
(150, 3, 'Dinesh', 'Anand', '41589fdd0f4220c50eab22259d45629b5bb0848f', 'sbdinesh@enoahisolution.com', '0', 0, '', '9790074370', 2, 0, '0', NULL, 'Thanks\nDinesh', 0, 0, 0),
(152, 17, 'Lokesh Babu', 'P', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'plokeshbabu@enoahisolution.com', NULL, 0, '9791069752', '9791069752', 5, 0, '0', NULL, NULL, 0, 0, 0),
(154, 16, 'Tejas HH', 'Babu', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'ptejas@enoahsolution.com', NULL, 0, '044-45512070', '9791069752', 4, 0, '0', NULL, NULL, 0, 0, 0),
(155, 14, 'Kumaran', 'Radhakrishnan', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'rkumaran@enoahisolution.com', NULL, 0, '', '9940175025', 2, 0, '0', NULL, NULL, 0, 0, 0),
(157, 3, 'anbu', 'r', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'ranburaj@enoahisolution.com', '0', 0, '', '', 5, 0, '0', NULL, 'Regards,\nAnbu', 0, 0, 0),
(158, 3, 'Sriram', 'S', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'ssriram@enoahisolution.com', '0', 0, '', '', 2, 0, '0', NULL, 'Thanks & Regards,\nSriram.S', 0, 0, 0),
(159, 8, 'Prem', 'Anand', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'mpremanand@enoahisolution.com', NULL, 0, '', '', 1, 0, '0', NULL, NULL, 0, 0, 0),
(160, 18, 'Raziya', 'Begum', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'sraziya@enoahisolution.com', NULL, 0, '', '', 3, 0, '0', NULL, NULL, 0, 0, 0),
(161, 18, 'Vijay Venkat', 'S', '41589fdd0f4220c50eab22259d45629b5bb0848f', 'svijay@enoahisolution.com', '0', 0, '', '', 2, 0, '0', NULL, 'Thanks, Vijay', 0, 0, 0),
(163, 2, 'vignesh', 'pr', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'prvignesh@enoahisolution.com', NULL, 0, '', '', 1, 0, '0', NULL, NULL, 0, 0, 0),
(164, 8, 'my', 'name', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'myname@enoah.in', NULL, 0, '', '', 5, 0, '0', NULL, NULL, 0, 0, 0),
(165, 8, 'new', 'n', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'new@enoahisolution.com', NULL, 0, '', '', 1, 0, '0', NULL, NULL, 0, 0, 0),
(167, 8, 'deepa', 'a', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'adeepa@enoahisolution.com', '0', 0, '', '8796541302', 2, 0, '0', NULL, 'Deepa.A', 0, 0, 0),
(168, 8, 'ganeshkumar', 'r', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'rganeshkumar@enoahisolution.com', NULL, 0, '', '', 3, 0, '0', NULL, NULL, 0, 0, 0),
(169, 17, 'priya l4', 'l', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'priyal4@enoah.in', NULL, 0, '', '', 4, 0, '0', NULL, NULL, 0, 0, 0),
(170, 2, 'Ganesh Kum', 'R', 'e29c94ab2807e62744a73ea4722bb2b4b213734e', 'rganesh@enoahisolution.com', '0', 0, '', '', 1, 0, '0', NULL, 'Test Signature', 0, 0, 0),
(171, 8, 'testing', 't', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'testing@enoahisolution.com', NULL, 0, '', '', 2, 0, '0', NULL, NULL, 0, 0, 0),
(172, 3, 'sathishkuamr', 'r', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'rsathishkumar@enoahisolution.com', '0', 0, '', '', 4, 0, '0', NULL, 'testststs', 0, 0, 0),
(173, 3, 'shankar', 'r', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'rshankar@enoahisolution.com', NULL, 0, '', '', 5, 0, '0', NULL, NULL, 0, 0, 0),
(174, 8, 'Ramakrishnan', 'V', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'vramakrishnan@enoahisolution.com', NULL, 0, '', '', 4, 0, '0', NULL, NULL, 0, 0, 0),
(185, 1, 'kumar', 'kooki', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'asdf@enoahisolution.com', NULL, 0, '123432121', '12132', 1, 0, '0', NULL, NULL, 0, 0, 0),
(188, 1, 'Peter', 'kein', '328854132bf61a37c6b4a64be7b23d03b74f8f83', 'pete@enoahisolution.com', NULL, 0, '123432121', '12132', 1, 0, '0', NULL, NULL, 0, 0, 0),
(189, 8, 'Govindaraju', 'V', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'vgovindaraju@enoahisolution.com', NULL, 0, '45454544', '123123', 2, 0, '0', NULL, NULL, 0, 0, 0);

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

INSERT INTO `crms_user_attendance` (`userid_fk`, `login_date`, `login_time`, `ip_addr`, `logout_time`, `tasks_today`, `tasks_nextday`) VALUES
(59, '2013-01-22', '2013-01-22 11:02:47', '192.168.1.74', NULL, NULL, NULL),
(118, '2013-01-22', '2013-01-22 16:44:41', '192.168.1.76', NULL, NULL, NULL),
(122, '2013-01-22', '2013-01-22 19:04:33', '122.174.68.192', NULL, NULL, NULL),
(59, '2013-01-23', '2013-01-23 10:31:48', '192.168.1.76', NULL, NULL, NULL),
(122, '2013-01-23', '2013-01-23 23:50:40', '122.174.37.238', NULL, NULL, NULL),
(122, '2013-01-24', '2013-01-24 00:00:01', '122.174.37.238', NULL, NULL, NULL),
(59, '2013-01-24', '2013-01-24 00:02:59', '122.174.37.238', NULL, NULL, NULL),
(118, '2013-01-24', '2013-01-24 00:05:44', '122.174.37.238', NULL, NULL, NULL),
(125, '2013-01-24', '2013-01-24 00:30:33', '192.168.0.214', NULL, NULL, NULL),
(126, '2013-01-24', '2013-01-24 18:56:32', '192.168.1.76', NULL, NULL, NULL),
(133, '2013-01-24', '2013-01-24 18:57:02', '192.168.1.76', NULL, NULL, NULL),
(124, '2013-01-24', '2013-01-24 19:00:58', '192.168.1.76', NULL, NULL, NULL),
(122, '2013-01-25', '2013-01-25 05:53:53', '192.168.0.213', NULL, NULL, NULL),
(118, '2013-01-25', '2013-01-25 09:36:03', '192.168.1.76', NULL, NULL, NULL),
(59, '2013-01-25', '2013-01-25 09:57:02', '192.168.1.76', NULL, NULL, NULL),
(139, '2013-01-25', '2013-01-25 11:06:29', '192.168.1.7', NULL, NULL, NULL),
(126, '2013-01-25', '2013-01-25 11:15:56', '192.168.14.25', NULL, NULL, NULL),
(133, '2013-01-25', '2013-01-25 11:24:13', '192.168.0.112', NULL, NULL, NULL),
(135, '2013-01-25', '2013-01-25 20:12:03', '192.168.1.74', NULL, NULL, NULL),
(118, '2013-01-28', '2013-01-28 07:16:39', '122.164.64.6', NULL, NULL, NULL),
(135, '2013-01-28', '2013-01-28 10:33:12', '192.168.1.74', NULL, NULL, NULL),
(140, '2013-01-28', '2013-01-28 12:07:22', '192.168.1.47', NULL, NULL, NULL),
(59, '2013-01-28', '2013-01-28 12:17:01', '192.168.1.74', NULL, NULL, NULL),
(139, '2013-01-28', '2013-01-28 13:47:02', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-01-29', '2013-01-29 10:54:30', '192.168.1.74', NULL, NULL, NULL),
(145, '2013-01-29', '2013-01-29 17:32:51', '192.168.1.22', NULL, NULL, NULL),
(118, '2013-01-29', '2013-01-29 17:53:32', '122.174.68.192', NULL, NULL, NULL),
(146, '2013-01-29', '2013-01-29 18:10:48', '192.168.1.22', NULL, NULL, NULL),
(145, '2013-01-30', '2013-01-30 12:42:15', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-01-30', '2013-01-30 15:46:34', '192.168.1.7', NULL, NULL, NULL),
(145, '2013-01-31', '2013-01-31 09:46:13', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-01-31', '2013-01-31 17:04:27', '192.168.1.74', NULL, NULL, NULL),
(59, '2013-02-01', '2013-02-01 09:26:20', '192.168.1.22', NULL, NULL, NULL),
(145, '2013-02-01', '2013-02-01 10:10:44', '192.168.1.22', NULL, NULL, NULL),
(147, '2013-02-01', '2013-02-01 11:02:29', '192.168.1.22', NULL, NULL, NULL),
(139, '2013-02-01', '2013-02-01 16:06:38', '192.168.1.7', NULL, NULL, NULL),
(135, '2013-02-01', '2013-02-01 21:32:48', '192.168.1.74', NULL, NULL, NULL),
(59, '2013-02-04', '2013-02-04 15:04:54', '192.168.1.74', NULL, NULL, NULL),
(135, '2013-02-04', '2013-02-04 15:06:00', '192.168.1.74', NULL, NULL, NULL),
(139, '2013-02-04', '2013-02-04 15:40:55', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-05', '2013-02-05 09:56:36', '192.168.0.45', NULL, NULL, NULL),
(149, '2013-02-05', '2013-02-05 11:02:18', '192.168.0.45', NULL, NULL, NULL),
(59, '2013-02-06', '2013-02-06 12:41:16', '192.168.1.74', NULL, NULL, NULL),
(59, '2013-02-07', '2013-02-07 09:38:42', '192.168.1.74', NULL, NULL, NULL),
(59, '2013-02-08', '2013-02-08 14:13:52', '192.168.0.6', NULL, NULL, NULL),
(59, '2013-02-12', '2013-02-12 11:49:28', '192.168.1.7', NULL, NULL, NULL),
(139, '2013-02-12', '2013-02-12 11:50:37', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-13', '2013-02-13 10:57:54', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-14', '2013-02-14 12:11:41', '192.168.1.7', NULL, NULL, NULL),
(139, '2013-02-14', '2013-02-14 16:48:55', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-15', '2013-02-15 10:35:52', '192.168.1.7', NULL, NULL, NULL),
(147, '2013-02-15', '2013-02-15 15:55:15', '192.168.0.45', NULL, NULL, NULL),
(150, '2013-02-15', '2013-02-15 16:30:09', '192.168.0.45', NULL, NULL, NULL),
(148, '2013-02-15', '2013-02-15 17:21:08', '192.168.0.45', NULL, NULL, NULL),
(145, '2013-02-15', '2013-02-15 17:44:00', '192.168.0.45', NULL, NULL, NULL),
(151, '2013-02-15', '2013-02-15 17:46:24', '192.168.0.45', NULL, NULL, NULL),
(149, '2013-02-15', '2013-02-15 23:31:24', '122.174.57.84', NULL, NULL, NULL),
(139, '2013-02-15', '2013-02-15 23:33:21', '122.174.57.84', NULL, NULL, NULL),
(59, '2013-02-16', '2013-02-16 00:01:53', '122.164.211.63', NULL, NULL, NULL),
(59, '2013-02-18', '2013-02-18 09:44:41', '192.168.1.7', NULL, NULL, NULL),
(150, '2013-02-18', '2013-02-18 12:40:38', '192.168.0.45', NULL, NULL, NULL),
(59, '2013-02-19', '2013-02-19 11:02:25', '192.168.0.45', NULL, NULL, NULL),
(150, '2013-02-19', '2013-02-19 11:13:29', '192.168.0.45', NULL, NULL, NULL),
(148, '2013-02-19', '2013-02-19 14:48:29', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-02-20', '2013-02-20 11:09:52', '192.168.0.127', NULL, NULL, NULL),
(148, '2013-02-20', '2013-02-20 11:30:21', '192.168.0.127', NULL, NULL, NULL),
(152, '2013-02-20', '2013-02-20 11:35:41', '192.168.0.127', NULL, NULL, NULL),
(139, '2013-02-20', '2013-02-20 12:17:15', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-21', '2013-02-21 14:36:57', '192.168.1.7', NULL, NULL, NULL),
(139, '2013-02-21', '2013-02-21 14:43:30', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-22', '2013-02-22 11:44:19', '192.168.0.127', NULL, NULL, NULL),
(135, '2013-02-22', '2013-02-22 17:50:59', '192.168.1.74', NULL, NULL, NULL),
(139, '2013-02-22', '2013-02-22 21:08:52', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-02-25', '2013-02-25 11:30:24', '192.168.0.127', NULL, NULL, NULL),
(151, '2013-02-25', '2013-02-25 12:01:21', '192.168.0.127', NULL, NULL, NULL),
(154, '2013-02-25', '2013-02-25 12:26:23', '192.168.0.127', NULL, NULL, NULL),
(148, '2013-02-25', '2013-02-25 12:27:36', '192.168.0.127', NULL, NULL, NULL),
(155, '2013-02-25', '2013-02-25 12:29:19', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-02-26', '2013-02-26 14:55:25', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-02-27', '2013-02-27 10:18:43', '192.168.0.6', NULL, NULL, NULL),
(155, '2013-02-27', '2013-02-27 10:24:26', '122.174.66.76', NULL, NULL, NULL),
(156, '2013-02-27', '2013-02-27 16:12:32', '192.168.1.73', NULL, NULL, NULL),
(59, '2013-03-06', '2013-03-06 13:02:58', '192.168.1.73', NULL, NULL, NULL),
(59, '2013-03-08', '2013-03-08 19:21:19', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-03-13', '2013-03-13 09:27:42', '10.0.9.1', NULL, NULL, NULL),
(139, '2013-03-13', '2013-03-13 10:50:33', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-14', '2013-03-14 09:47:59', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-15', '2013-03-15 10:47:08', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-16', '2013-03-16 14:19:11', '10.0.9.1', NULL, NULL, NULL),
(139, '2013-03-18', '2013-03-18 19:34:23', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-19', '2013-03-19 17:43:12', '10.0.9.1', NULL, NULL, NULL),
(139, '2013-03-19', '2013-03-19 19:22:41', '10.0.9.1', NULL, NULL, NULL),
(156, '2013-03-19', '2013-03-19 20:25:48', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-20', '2013-03-20 11:57:35', '10.0.9.1', NULL, NULL, NULL),
(139, '2013-03-20', '2013-03-20 19:10:55', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-21', '2013-03-21 10:47:26', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-22', '2013-03-22 17:12:38', '10.0.9.1', NULL, NULL, NULL),
(150, '2013-03-22', '2013-03-22 17:15:59', '10.0.9.1', NULL, NULL, NULL),
(145, '2013-03-22', '2013-03-22 17:17:37', '10.0.9.1', NULL, NULL, NULL),
(148, '2013-03-22', '2013-03-22 17:17:54', '10.0.9.1', NULL, NULL, NULL),
(59, '2013-03-25', '2013-03-25 15:19:26', '192.168.1.7', NULL, NULL, NULL),
(139, '2013-03-25', '2013-03-25 15:20:35', '192.168.1.7', NULL, NULL, NULL),
(156, '2013-03-25', '2013-03-25 17:13:39', '192.168.1.104', NULL, NULL, NULL),
(158, '2013-03-25', '2013-03-25 18:55:29', '192.168.0.45', NULL, NULL, NULL),
(150, '2013-03-25', '2013-03-25 20:33:11', '192.168.0.45', NULL, NULL, NULL),
(159, '2013-03-25', '2013-03-25 20:52:39', '192.168.0.45', NULL, NULL, NULL),
(59, '2013-03-26', '2013-03-26 10:01:25', '192.168.1.73', NULL, NULL, NULL),
(139, '2013-03-26', '2013-03-26 10:30:05', '10.0.9.1', NULL, NULL, NULL),
(139, '2013-03-27', '2013-03-27 14:20:28', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-03-27', '2013-03-27 14:20:54', '192.168.1.7', NULL, NULL, NULL),
(160, '2013-03-27', '2013-03-27 15:48:09', '192.168.1.22', NULL, NULL, NULL),
(59, '2013-03-29', '2013-03-29 10:55:50', '192.168.0.127', NULL, NULL, NULL),
(148, '2013-03-29', '2013-03-29 12:22:09', '192.168.0.127', NULL, NULL, NULL),
(161, '2013-03-29', '2013-03-29 12:56:44', '192.168.0.127', NULL, NULL, NULL),
(139, '2013-03-29', '2013-03-29 14:37:44', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-04-01', '2013-04-01 11:01:27', '192.168.0.127', NULL, NULL, NULL),
(161, '2013-04-01', '2013-04-01 11:14:53', '192.168.0.127', NULL, NULL, NULL),
(158, '2013-04-01', '2013-04-01 11:45:40', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-04-02', '2013-04-02 11:57:24', '192.168.0.127', NULL, NULL, NULL),
(161, '2013-04-02', '2013-04-02 11:57:47', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-04-03', '2013-04-03 19:10:32', '192.168.1.73', NULL, NULL, NULL),
(161, '2013-04-03', '2013-04-03 19:34:54', '192.168.1.73', NULL, NULL, NULL),
(59, '2013-04-09', '2013-04-09 11:09:24', '192.168.0.127', NULL, NULL, NULL),
(59, '2013-04-10', '2013-04-10 16:32:52', '192.168.1.73', NULL, NULL, NULL),
(139, '2013-04-11', '2013-04-11 15:39:23', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-04-12', '2013-04-12 10:36:32', '192.168.1.73', NULL, NULL, NULL),
(59, '2013-04-15', '2013-04-15 14:47:03', '192.168.1.73', NULL, NULL, NULL),
(139, '2013-04-15', '2013-04-15 17:16:24', '192.168.1.7', NULL, NULL, NULL),
(158, '2013-04-15', '2013-04-15 18:40:36', '192.168.1.7', NULL, NULL, NULL),
(162, '2013-04-15', '2013-04-15 13:54:48', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-04-16', '2013-04-16 05:29:31', '192.168.1.73', NULL, NULL, NULL),
(159, '2013-04-16', '2013-04-16 05:48:08', '192.168.1.73', NULL, NULL, NULL),
(139, '2013-04-16', '2013-04-16 05:52:06', '192.168.1.73', NULL, NULL, NULL),
(149, '2013-04-16', '2013-04-16 05:53:24', '192.168.1.73', NULL, NULL, NULL),
(163, '2013-04-16', '2013-04-16 13:12:17', '::1', NULL, NULL, NULL),
(158, '2013-04-16', '2013-04-16 19:13:52', '::1', NULL, NULL, NULL),
(59, '2013-04-17', '2013-04-17 09:43:10', '::1', NULL, NULL, NULL),
(165, '2013-04-17', '2013-04-17 11:26:34', '::1', NULL, NULL, NULL),
(159, '2013-04-17', '2013-04-17 12:28:40', '::1', NULL, NULL, NULL),
(59, '2013-04-18', '2013-04-18 10:27:30', '::1', NULL, NULL, NULL),
(139, '2013-04-18', '2013-04-18 11:35:48', '::1', NULL, NULL, NULL),
(158, '2013-04-18', '2013-04-18 18:44:37', '::1', NULL, NULL, NULL),
(135, '2013-04-18', '2013-04-18 19:53:21', '192.168.1.7', NULL, NULL, NULL),
(158, '2013-04-19', '2013-04-19 10:20:34', '::1', NULL, NULL, NULL),
(59, '2013-04-19', '2013-04-19 14:52:25', '::1', NULL, NULL, NULL),
(139, '2013-04-19', '2013-04-19 17:40:53', '::1', NULL, NULL, NULL),
(59, '2013-04-22', '2013-04-22 10:25:37', '::1', NULL, NULL, NULL),
(139, '2013-04-22', '2013-04-22 10:46:26', '::1', NULL, NULL, NULL),
(158, '2013-04-22', '2013-04-22 10:59:53', '::1', NULL, NULL, NULL),
(59, '2013-04-23', '2013-04-23 10:44:39', '::1', NULL, NULL, NULL),
(59, '2013-04-24', '2013-04-24 10:16:17', '::1', NULL, NULL, NULL),
(139, '2013-04-24', '2013-04-24 11:13:08', '::1', NULL, NULL, NULL),
(161, '2013-04-24', '2013-04-24 13:05:43', '::1', NULL, NULL, NULL),
(158, '2013-04-24', '2013-04-24 14:57:08', '::1', NULL, NULL, NULL),
(59, '2013-04-25', '2013-04-25 10:18:19', '::1', NULL, NULL, NULL),
(163, '2013-04-25', '2013-04-25 10:45:11', '192.168.1.49', NULL, NULL, NULL),
(158, '2013-04-25', '2013-04-25 11:04:11', '::1', NULL, NULL, NULL),
(161, '2013-04-25', '2013-04-25 12:56:26', '::1', NULL, NULL, NULL),
(139, '2013-04-25', '2013-04-25 14:48:58', '::1', NULL, NULL, NULL),
(167, '2013-04-25', '2013-04-25 15:34:51', '::1', NULL, NULL, NULL),
(59, '2013-04-26', '2013-04-26 10:00:08', '::1', NULL, NULL, NULL),
(167, '2013-04-26', '2013-04-26 10:00:18', '::1', NULL, NULL, NULL),
(147, '2013-04-26', '2013-04-26 10:46:32', '::1', NULL, NULL, NULL),
(168, '2013-04-26', '2013-04-26 10:57:10', '::1', NULL, NULL, NULL),
(155, '2013-04-26', '2013-04-26 11:04:18', '::1', NULL, NULL, NULL),
(161, '2013-04-26', '2013-04-26 11:08:56', '::1', NULL, NULL, NULL),
(139, '2013-04-26', '2013-04-26 11:47:57', '::1', NULL, NULL, NULL),
(149, '2013-04-26', '2013-04-26 12:47:01', '::1', NULL, NULL, NULL),
(158, '2013-04-26', '2013-04-26 14:34:40', '::1', NULL, NULL, NULL),
(165, '2013-04-26', '2013-04-26 14:35:11', '::1', NULL, NULL, NULL),
(164, '2013-04-26', '2013-04-26 14:37:44', '::1', NULL, NULL, NULL),
(169, '2013-04-26', '2013-04-26 15:34:49', '::1', NULL, NULL, NULL),
(59, '2013-04-29', '2013-04-29 10:21:31', '::1', NULL, NULL, NULL),
(139, '2013-04-29', '2013-04-29 10:26:21', '::1', NULL, NULL, NULL),
(168, '2013-04-29', '2013-04-29 11:00:08', '::1', NULL, NULL, NULL),
(161, '2013-04-29', '2013-04-29 11:38:45', '::1', NULL, NULL, NULL),
(164, '2013-04-29', '2013-04-29 11:53:07', '::1', NULL, NULL, NULL),
(149, '2013-04-29', '2013-04-29 11:54:10', '::1', NULL, NULL, NULL),
(59, '2013-04-30', '2013-04-30 10:53:49', '::1', NULL, NULL, NULL),
(139, '2013-04-30', '2013-04-30 11:41:29', '::1', NULL, NULL, NULL),
(161, '2013-04-30', '2013-04-30 11:41:50', '::1', NULL, NULL, NULL),
(149, '2013-04-30', '2013-04-30 11:51:42', '::1', NULL, NULL, NULL),
(169, '2013-04-30', '2013-04-30 11:55:40', '::1', NULL, NULL, NULL),
(158, '2013-04-30', '2013-04-30 12:04:40', '::1', NULL, NULL, NULL),
(167, '2013-04-30', '2013-04-30 12:06:19', '::1', NULL, NULL, NULL),
(59, '2013-05-02', '2013-05-02 10:56:12', '::1', NULL, NULL, NULL),
(161, '2013-05-02', '2013-05-02 11:04:48', '::1', NULL, NULL, NULL),
(167, '2013-05-02', '2013-05-02 11:05:46', '::1', NULL, NULL, NULL),
(147, '2013-05-02', '2013-05-02 11:06:59', '::1', NULL, NULL, NULL),
(139, '2013-05-02', '2013-05-02 17:31:44', '::1', NULL, NULL, NULL),
(59, '2013-05-03', '2013-05-03 10:09:49', '::1', NULL, NULL, NULL),
(139, '2013-05-03', '2013-05-03 10:45:55', '::1', NULL, NULL, NULL),
(158, '2013-05-03', '2013-05-03 10:46:23', '::1', NULL, NULL, NULL),
(161, '2013-05-03', '2013-05-03 11:32:07', '::1', NULL, NULL, NULL),
(167, '2013-05-03', '2013-05-03 18:40:39', '192.168.1.7', NULL, NULL, NULL),
(59, '2013-05-06', '2013-05-06 11:17:17', '::1', NULL, NULL, NULL),
(139, '2013-05-06', '2013-05-06 16:18:22', '::1', NULL, NULL, NULL),
(158, '2013-05-06', '2013-05-06 20:29:49', '::1', NULL, NULL, NULL),
(59, '2013-05-07', '2013-05-07 09:56:58', '::1', NULL, NULL, NULL),
(59, '2013-05-08', '2013-05-08 13:09:42', '::1', NULL, NULL, NULL),
(139, '2013-05-09', '2013-05-09 10:36:58', '::1', NULL, NULL, NULL),
(59, '2013-05-09', '2013-05-09 10:48:28', '::1', NULL, NULL, NULL),
(59, '2013-05-10', '2013-05-10 10:23:40', '::1', NULL, NULL, NULL),
(59, '2013-05-11', '2013-05-11 14:22:05', '::1', NULL, NULL, NULL),
(59, '2013-05-13', '2013-05-13 09:57:43', '::1', NULL, NULL, NULL),
(59, '2013-05-14', '2013-05-14 12:38:46', '::1', NULL, NULL, NULL),
(59, '2013-05-16', '2013-05-16 11:19:36', '::1', NULL, NULL, NULL),
(139, '2013-05-16', '2013-05-16 11:45:44', '::1', NULL, NULL, NULL),
(158, '2013-05-16', '2013-05-16 11:46:34', '::1', NULL, NULL, NULL),
(161, '2013-05-16', '2013-05-16 11:47:25', '::1', NULL, NULL, NULL),
(59, '2013-05-20', '2013-05-20 12:35:30', '::1', NULL, NULL, NULL),
(59, '2013-05-23', '2013-05-23 19:45:38', '::1', NULL, NULL, NULL),
(59, '2013-05-27', '2013-05-27 15:02:45', '::1', NULL, NULL, NULL),
(59, '2013-05-29', '2013-05-29 20:48:42', '::1', NULL, NULL, NULL),
(59, '2013-06-03', '2013-06-03 19:36:16', '::1', NULL, NULL, NULL),
(59, '2013-06-05', '2013-06-05 12:44:03', '::1', NULL, NULL, NULL),
(59, '2013-06-06', '2013-06-06 14:21:25', '::1', NULL, NULL, NULL),
(59, '2013-06-11', '2013-06-11 18:20:36', '::1', NULL, NULL, NULL),
(59, '2013-06-14', '2013-06-14 12:12:47', '::1', NULL, NULL, NULL),
(59, '2013-06-18', '2013-06-18 15:58:56', '::1', NULL, NULL, NULL),
(59, '2013-06-24', '2013-06-24 14:22:20', '::1', NULL, NULL, NULL),
(59, '2013-06-26', '2013-06-26 15:04:41', '::1', NULL, NULL, NULL),
(59, '2013-06-27', '2013-06-27 13:06:47', '::1', NULL, NULL, NULL),
(59, '2013-07-08', '2013-07-08 13:29:32', '::1', NULL, NULL, NULL),
(170, '2013-07-08', '2013-07-08 14:37:39', '::1', NULL, NULL, NULL),
(158, '2013-07-08', '2013-07-08 19:01:03', '::1', NULL, NULL, NULL),
(59, '2013-07-09', '2013-07-09 10:04:59', '::1', NULL, NULL, NULL),
(167, '2013-07-09', '2013-07-09 12:34:55', '::1', NULL, NULL, NULL),
(160, '2013-07-09', '2013-07-09 12:42:09', '::1', NULL, NULL, NULL),
(170, '2013-07-09', '2013-07-09 13:21:58', '::1', NULL, NULL, NULL),
(135, '2013-07-09', '2013-07-09 14:35:08', '::1', NULL, NULL, NULL),
(155, '2013-07-09', '2013-07-09 14:37:06', '::1', NULL, NULL, NULL),
(161, '2013-07-09', '2013-07-09 14:48:32', '::1', NULL, NULL, NULL),
(147, '2013-07-09', '2013-07-09 15:05:40', '::1', NULL, NULL, NULL),
(59, '2013-07-10', '2013-07-10 08:29:01', '::1', NULL, NULL, NULL),
(160, '2013-07-10', '2013-07-10 08:49:51', '::1', NULL, NULL, NULL),
(161, '2013-07-10', '2013-07-10 11:16:25', '::1', NULL, NULL, NULL),
(59, '2013-07-11', '2013-07-11 11:05:15', '::1', NULL, NULL, NULL),
(158, '2013-07-11', '2013-07-11 17:01:52', '::1', NULL, NULL, NULL),
(59, '2013-07-12', '2013-07-12 15:10:28', '::1', NULL, NULL, NULL),
(59, '2013-07-15', '2013-07-15 10:22:32', '::1', NULL, NULL, NULL),
(59, '2013-07-16', '2013-07-16 09:10:54', '::1', NULL, NULL, NULL),
(59, '2013-07-17', '2013-07-17 10:30:31', '::1', NULL, NULL, NULL),
(59, '2013-07-18', '2013-07-18 12:18:38', '::1', NULL, NULL, NULL),
(139, '2013-07-18', '2013-07-18 19:00:10', '::1', NULL, NULL, NULL),
(158, '2013-07-18', '2013-07-18 21:14:24', '::1', NULL, NULL, NULL),
(160, '2013-07-18', '2013-07-18 21:16:46', '::1', NULL, NULL, NULL),
(59, '2013-07-19', '2013-07-19 10:40:14', '::1', NULL, NULL, NULL),
(170, '2013-07-19', '2013-07-19 18:47:54', '::1', NULL, NULL, NULL),
(59, '2013-07-22', '2013-07-22 10:21:36', '::1', NULL, NULL, NULL),
(139, '2013-07-22', '2013-07-22 10:21:50', '::1', NULL, NULL, NULL),
(167, '2013-07-22', '2013-07-22 11:41:32', '::1', NULL, NULL, NULL),
(158, '2013-07-22', '2013-07-22 14:36:30', '::1', NULL, NULL, NULL),
(163, '2013-07-22', '2013-07-22 14:38:35', '::1', NULL, NULL, NULL),
(59, '2013-07-23', '2013-07-23 09:51:13', '::1', NULL, NULL, NULL),
(59, '2013-07-24', '2013-07-24 10:23:32', '::1', NULL, NULL, NULL),
(59, '2013-07-25', '2013-07-25 10:51:31', '::1', NULL, NULL, NULL),
(59, '2013-07-26', '2013-07-26 10:33:46', '::1', NULL, NULL, NULL),
(59, '2013-07-29', '2013-07-29 10:26:51', '::1', NULL, NULL, NULL),
(158, '2013-07-29', '2013-07-29 14:43:37', '::1', NULL, NULL, NULL),
(167, '2013-07-29', '2013-07-29 14:46:49', '::1', NULL, NULL, NULL),
(139, '2013-07-29', '2013-07-29 16:49:54', '127.0.0.1', NULL, NULL, NULL),
(168, '2013-07-29', '2013-07-29 17:58:42', '127.0.0.1', NULL, NULL, NULL),
(170, '2013-07-29', '2013-07-29 18:04:28', '::1', NULL, NULL, NULL),
(161, '2013-07-29', '2013-07-29 19:05:14', '::1', NULL, NULL, NULL),
(172, '2013-07-29', '2013-07-29 19:30:16', '::1', NULL, NULL, NULL),
(173, '2013-07-29', '2013-07-29 19:35:36', '::1', NULL, NULL, NULL),
(59, '2013-07-30', '2013-07-30 10:31:20', '::1', NULL, NULL, NULL),
(158, '2013-07-30', '2013-07-30 10:36:15', '::1', NULL, NULL, NULL),
(168, '2013-07-30', '2013-07-30 11:14:43', '::1', NULL, NULL, NULL),
(172, '2013-07-30', '2013-07-30 11:16:14', '::1', NULL, NULL, NULL),
(173, '2013-07-30', '2013-07-30 11:17:09', '::1', NULL, NULL, NULL),
(161, '2013-07-30', '2013-07-30 12:05:02', '::1', NULL, NULL, NULL),
(174, '2013-07-30', '2013-07-30 14:33:40', '127.0.0.1', NULL, NULL, NULL),
(149, '2013-07-30', '2013-07-30 15:01:51', '::1', NULL, NULL, NULL),
(59, '2013-07-31', '2013-07-31 10:16:04', '::1', NULL, NULL, NULL),
(158, '2013-07-31', '2013-07-31 14:19:50', '::1', NULL, NULL, NULL),
(168, '2013-07-31', '2013-07-31 14:47:48', '::1', NULL, NULL, NULL),
(172, '2013-07-31', '2013-07-31 14:56:37', '::1', NULL, NULL, NULL),
(173, '2013-07-31', '2013-07-31 14:57:03', '::1', NULL, NULL, NULL),
(59, '2013-08-01', '2013-08-01 09:58:54', '::1', NULL, NULL, NULL),
(158, '2013-08-01', '2013-08-01 12:32:31', '::1', NULL, NULL, NULL),
(172, '2013-08-01', '2013-08-01 12:43:31', '::1', NULL, NULL, NULL),
(173, '2013-08-01', '2013-08-01 12:43:59', '::1', NULL, NULL, NULL),
(59, '2013-08-02', '2013-08-02 10:18:11', '::1', NULL, NULL, NULL),
(158, '2013-08-02', '2013-08-02 14:41:17', '::1', NULL, NULL, NULL),
(168, '2013-08-02', '2013-08-02 14:41:32', '::1', NULL, NULL, NULL),
(173, '2013-08-02', '2013-08-02 14:41:47', '::1', NULL, NULL, NULL),
(59, '2013-08-05', '2013-08-05 10:40:33', '::1', NULL, NULL, NULL),
(158, '2013-08-05', '2013-08-05 14:56:25', '::1', NULL, NULL, NULL),
(59, '2013-08-06', '2013-08-06 10:03:37', '::1', NULL, NULL, NULL),
(158, '2013-08-06', '2013-08-06 18:14:36', '::1', NULL, NULL, NULL),
(168, '2013-08-06', '2013-08-06 18:17:15', '::1', NULL, NULL, NULL),
(59, '2013-08-07', '2013-08-07 10:11:15', '::1', NULL, NULL, NULL),
(167, '2013-08-07', '2013-08-07 18:14:43', '::1', NULL, NULL, NULL),
(59, '2013-08-08', '2013-08-08 10:27:04', '::1', NULL, NULL, NULL),
(158, '2013-08-08', '2013-08-08 15:10:31', '::1', NULL, NULL, NULL),
(152, '2013-08-08', '2013-08-08 21:44:21', '::1', NULL, NULL, NULL),
(59, '2013-08-09', '2013-08-09 10:10:40', '::1', NULL, NULL, NULL),
(152, '2013-08-09', '2013-08-09 10:11:01', '::1', NULL, NULL, NULL),
(158, '2013-08-09', '2013-08-09 15:21:44', '::1', NULL, NULL, NULL),
(59, '2013-08-12', '2013-08-12 10:30:48', '::1', NULL, NULL, NULL),
(59, '2013-08-13', '2013-08-13 10:14:47', '::1', NULL, NULL, NULL),
(158, '2013-08-13', '2013-08-13 18:59:09', '::1', NULL, NULL, NULL),
(168, '2013-08-13', '2013-08-13 18:59:55', '::1', NULL, NULL, NULL),
(172, '2013-08-13', '2013-08-13 19:00:32', '::1', NULL, NULL, NULL),
(173, '2013-08-13', '2013-08-13 19:02:14', '::1', NULL, NULL, NULL),
(59, '2013-08-14', '2013-08-14 10:45:53', '::1', NULL, NULL, NULL),
(59, '2013-08-16', '2013-08-16 10:20:08', '::1', NULL, NULL, NULL),
(158, '2013-08-16', '2013-08-16 12:42:17', '::1', NULL, NULL, NULL),
(59, '2013-08-19', '2013-08-19 10:20:40', '::1', NULL, NULL, NULL),
(59, '2013-08-20', '2013-08-20 10:45:45', '::1', NULL, NULL, NULL),
(158, '2013-08-20', '2013-08-20 19:44:41', '::1', NULL, NULL, NULL),
(59, '2013-08-21', '2013-08-21 10:13:08', '::1', NULL, NULL, NULL),
(158, '2013-08-21', '2013-08-21 10:13:36', '::1', NULL, NULL, NULL),
(161, '2013-08-21', '2013-08-21 11:08:47', '::1', NULL, NULL, NULL),
(59, '2013-08-22', '2013-08-22 10:29:34', '::1', NULL, NULL, NULL),
(59, '2013-08-23', '2013-08-23 10:42:04', '::1', NULL, NULL, NULL),
(59, '2013-08-26', '2013-08-26 10:08:54', '::1', NULL, NULL, NULL),
(158, '2013-08-26', '2013-08-26 12:09:52', '::1', NULL, NULL, NULL),
(168, '2013-08-26', '2013-08-26 12:10:13', '::1', NULL, NULL, NULL),
(173, '2013-08-26', '2013-08-26 12:11:01', '::1', NULL, NULL, NULL),
(59, '2013-08-27', '2013-08-27 10:25:18', '::1', NULL, NULL, NULL),
(154, '2013-08-27', '2013-08-27 16:38:42', '::1', NULL, NULL, NULL),
(152, '2013-08-27', '2013-08-27 17:36:57', '::1', NULL, NULL, NULL),
(59, '2013-08-28', '2013-08-28 10:05:01', '::1', NULL, NULL, NULL),
(158, '2013-08-28', '2013-08-28 15:23:49', '::1', NULL, NULL, NULL),
(59, '2013-08-29', '2013-08-29 10:12:58', '::1', NULL, NULL, NULL),
(157, '2013-08-29', '2013-08-29 10:37:08', '::1', NULL, NULL, NULL),
(161, '2013-08-29', '2013-08-29 11:51:13', '::1', NULL, NULL, NULL),
(173, '2013-08-29', '2013-08-29 11:52:09', '::1', NULL, NULL, NULL),
(158, '2013-08-29', '2013-08-29 12:02:52', '::1', NULL, NULL, NULL),
(59, '2013-08-30', '2013-08-30 10:41:41', '::1', NULL, NULL, NULL),
(59, '2013-08-31', '2013-08-31 12:10:23', '::1', NULL, NULL, NULL),
(59, '2013-09-02', '2013-09-02 10:24:44', '::1', NULL, NULL, NULL),
(59, '2013-09-03', '2013-09-03 09:54:09', '::1', NULL, NULL, NULL),
(59, '2013-09-04', '2013-09-04 10:13:07', '::1', NULL, NULL, NULL),
(158, '2013-09-04', '2013-09-04 15:52:10', '::1', NULL, NULL, NULL),
(152, '2013-09-04', '2013-09-04 19:45:08', '::1', NULL, NULL, NULL),
(59, '2013-09-05', '2013-09-05 10:46:33', '::1', NULL, NULL, NULL),
(152, '2013-09-05', '2013-09-05 11:39:00', '::1', NULL, NULL, NULL),
(158, '2013-09-05', '2013-09-05 16:39:10', '::1', NULL, NULL, NULL),
(59, '2013-09-06', '2013-09-06 08:35:18', '::1', NULL, NULL, NULL),
(59, '2013-09-11', '2013-09-11 11:17:52', '::1', NULL, NULL, NULL),
(152, '2013-09-11', '2013-09-11 17:14:54', '::1', NULL, NULL, NULL),
(59, '2013-09-12', '2013-09-12 11:31:55', '::1', NULL, NULL, NULL),
(152, '2013-09-12', '2013-09-12 12:54:52', '::1', NULL, NULL, NULL),
(149, '2013-09-12', '2013-09-12 15:25:16', '::1', NULL, NULL, NULL),
(154, '2013-09-12', '2013-09-12 15:27:09', '::1', NULL, NULL, NULL),
(59, '2013-09-13', '2013-09-13 10:06:59', '::1', NULL, NULL, NULL),
(59, '2013-09-16', '2013-09-16 10:16:49', '::1', NULL, NULL, NULL),
(158, '2013-09-16', '2013-09-16 12:53:50', '::1', NULL, NULL, NULL),
(168, '2013-09-16', '2013-09-16 13:02:00', '::1', NULL, NULL, NULL),
(172, '2013-09-16', '2013-09-16 13:02:43', '::1', NULL, NULL, NULL),
(173, '2013-09-16', '2013-09-16 13:02:57', '::1', NULL, NULL, NULL),
(149, '2013-09-16', '2013-09-16 13:36:42', '::1', NULL, NULL, NULL),
(59, '2013-09-17', '2013-09-17 10:23:25', '::1', NULL, NULL, NULL),
(155, '2013-09-17', '2013-09-17 11:11:24', '::1', NULL, NULL, NULL),
(158, '2013-09-17', '2013-09-17 12:26:56', '::1', NULL, NULL, NULL),
(168, '2013-09-17', '2013-09-17 12:27:28', '::1', NULL, NULL, NULL),
(172, '2013-09-17', '2013-09-17 12:27:42', '::1', NULL, NULL, NULL),
(59, '2013-09-18', '2013-09-18 10:25:57', '::1', NULL, NULL, NULL),
(59, '2013-09-19', '2013-09-19 10:27:58', '::1', NULL, NULL, NULL),
(59, '2013-09-20', '2013-09-20 10:42:48', '::1', NULL, NULL, NULL),
(59, '2013-09-23', '2013-09-23 10:35:50', '::1', NULL, NULL, NULL),
(158, '2013-09-23', '2013-09-23 15:57:47', '::1', NULL, NULL, NULL),
(168, '2013-09-23', '2013-09-23 16:02:02', '::1', NULL, NULL, NULL),
(158, '2013-09-24', '2013-09-24 09:49:10', '::1', NULL, NULL, NULL),
(59, '2013-09-24', '2013-09-24 14:28:40', '::1', NULL, NULL, NULL),
(147, '2013-09-24', '2013-09-24 16:13:17', '::1', NULL, NULL, NULL),
(174, '2013-09-24', '2013-09-24 16:18:07', '::1', NULL, NULL, NULL),
(139, '2013-09-24', '2013-09-24 16:18:33', '::1', NULL, NULL, NULL),
(173, '2013-09-24', '2013-09-24 16:18:51', '::1', NULL, NULL, NULL),
(172, '2013-09-24', '2013-09-24 16:19:10', '::1', NULL, NULL, NULL),
(59, '2013-09-25', '2013-09-25 10:11:09', '::1', NULL, NULL, NULL),
(59, '2013-09-26', '2013-09-26 10:43:44', '::1', NULL, NULL, NULL),
(158, '2013-09-26', '2013-09-26 10:44:12', '::1', NULL, NULL, NULL),
(160, '2013-09-26', '2013-09-26 17:47:57', '::1', NULL, NULL, NULL),
(161, '2013-09-26', '2013-09-26 17:48:43', '::1', NULL, NULL, NULL),
(161, '2013-09-27', '2013-09-27 10:24:27', '::1', NULL, NULL, NULL),
(158, '2013-09-27', '2013-09-27 10:55:52', '::1', NULL, NULL, NULL),
(160, '2013-09-27', '2013-09-27 11:27:27', '::1', NULL, NULL, NULL),
(59, '2013-09-27', '2013-09-27 11:28:56', '::1', NULL, NULL, NULL),
(152, '2013-09-27', '2013-09-27 11:29:33', '::1', NULL, NULL, NULL),
(155, '2013-09-27', '2013-09-27 16:31:35', '::1', NULL, NULL, NULL),
(147, '2013-09-27', '2013-09-27 17:07:02', '::1', NULL, NULL, NULL),
(59, '2013-09-30', '2013-09-30 10:03:10', '::1', NULL, NULL, NULL),
(158, '2013-09-30', '2013-09-30 11:07:18', '::1', NULL, NULL, NULL),
(174, '2013-09-30', '2013-09-30 15:04:16', '::1', NULL, NULL, NULL),
(170, '2013-09-30', '2013-09-30 15:39:45', '::1', NULL, NULL, NULL),
(168, '2013-09-30', '2013-09-30 15:40:05', '::1', NULL, NULL, NULL),
(161, '2013-09-30', '2013-09-30 15:48:45', '::1', NULL, NULL, NULL),
(152, '2013-09-30', '2013-09-30 15:49:18', '::1', NULL, NULL, NULL),
(160, '2013-09-30', '2013-09-30 18:03:19', '::1', NULL, NULL, NULL),
(172, '2013-09-30', '2013-09-30 19:31:05', '::1', NULL, NULL, NULL),
(173, '2013-09-30', '2013-09-30 19:32:58', '::1', NULL, NULL, NULL),
(158, '2013-10-01', '2013-10-01 10:13:31', '::1', NULL, NULL, NULL),
(59, '2013-10-01', '2013-10-01 15:15:26', '192.168.1.53', NULL, NULL, NULL),
(59, '2013-10-03', '2013-10-03 10:49:15', '::1', NULL, NULL, NULL),
(158, '2013-10-03', '2013-10-03 14:32:03', '192.168.1.53', NULL, NULL, NULL),
(59, '2013-10-04', '2013-10-04 10:27:52', '::1', NULL, NULL, NULL),
(59, '2013-10-05', '2013-10-05 15:09:25', '::1', NULL, NULL, NULL),
(59, '2013-10-07', '2013-10-07 10:27:02', '::1', NULL, NULL, NULL),
(158, '2013-10-07', '2013-10-07 12:21:47', '::1', NULL, NULL, NULL),
(59, '2013-10-08', '2013-10-08 11:17:43', '::1', NULL, NULL, NULL),
(158, '2013-10-08', '2013-10-08 15:52:32', '::1', NULL, NULL, NULL),
(59, '2013-10-09', '2013-10-09 10:23:13', '::1', NULL, NULL, NULL),
(152, '2013-10-09', '2013-10-09 14:31:13', '::1', NULL, NULL, NULL),
(149, '2013-10-09', '2013-10-09 14:31:26', '::1', NULL, NULL, NULL),
(154, '2013-10-09', '2013-10-09 14:31:43', '::1', NULL, NULL, NULL),
(59, '2013-10-11', '2013-10-11 10:28:12', '::1', NULL, NULL, NULL),
(59, '2013-10-15', '2013-10-15 10:54:42', '::1', NULL, NULL, NULL),
(149, '2013-10-15', '2013-10-15 17:52:48', '::1', NULL, NULL, NULL),
(152, '2013-10-15', '2013-10-15 17:52:58', '::1', NULL, NULL, NULL),
(59, '2013-10-16', '2013-10-16 10:42:34', '::1', NULL, NULL, NULL),
(158, '2013-10-16', '2013-10-16 17:12:55', '::1', NULL, NULL, NULL),
(59, '2013-10-17', '2013-10-17 10:23:26', '::1', NULL, NULL, NULL),
(59, '2013-10-18', '2013-10-18 11:25:18', '::1', NULL, NULL, NULL),
(59, '2013-10-21', '2013-10-21 10:18:44', '::1', NULL, NULL, NULL),
(59, '2013-10-22', '2013-10-22 11:46:25', '::1', NULL, NULL, NULL),
(59, '2013-10-23', '2013-10-23 10:57:16', '::1', NULL, NULL, NULL),
(59, '2013-10-24', '2013-10-24 10:00:33', '::1', NULL, NULL, NULL),
(59, '2013-10-25', '2013-10-25 10:53:39', '::1', NULL, NULL, NULL),
(59, '2013-10-26', '2013-10-26 12:21:44', '::1', NULL, NULL, NULL),
(59, '2013-10-28', '2013-10-28 10:29:16', '::1', NULL, NULL, NULL),
(59, '2013-10-29', '2013-10-29 10:42:09', '::1', NULL, NULL, NULL),
(152, '2013-10-29', '2013-10-29 21:48:53', '::1', NULL, NULL, NULL),
(59, '2013-10-30', '2013-10-30 10:02:33', '::1', NULL, NULL, NULL),
(152, '2013-10-30', '2013-10-30 10:02:41', '::1', NULL, NULL, NULL),
(59, '2013-10-31', '2013-10-31 10:08:35', '::1', NULL, NULL, NULL),
(59, '2013-11-04', '2013-11-04 10:23:07', '::1', NULL, NULL, NULL),
(158, '2013-11-04', '2013-11-04 17:48:12', '::1', NULL, NULL, NULL),
(59, '2013-11-05', '2013-11-05 10:07:02', '::1', NULL, NULL, NULL),
(59, '2013-11-06', '2013-11-06 09:29:41', '::1', NULL, NULL, NULL),
(158, '2013-11-06', '2013-11-06 09:29:46', '::1', NULL, NULL, NULL),
(163, '2013-11-06', '2013-11-06 18:05:51', '::1', NULL, NULL, NULL),
(59, '2013-11-07', '2013-11-07 09:54:15', '::1', NULL, NULL, NULL),
(59, '2013-11-15', '2013-11-15 09:52:13', '::1', NULL, NULL, NULL),
(59, '2013-11-18', '2013-11-18 10:12:58', '::1', NULL, NULL, NULL),
(189, '2013-11-18', '2013-11-18 12:23:10', '::1', NULL, NULL, NULL),
(59, '2013-11-19', '2013-11-19 10:10:09', '::1', NULL, NULL, NULL);

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
