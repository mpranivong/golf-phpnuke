<?php
if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}

function adterms() {
	global $golf_config, $nukeurl, $sitename;
/**
 * Customize for each application
 */
$site_url = $nukeurl;
$site_name = $sitename;
$site_email = $golf_config['advertising_email'];
$site_mail_address = $golf_config['mail_address'];

$s = '<table align="center">
<tr><td>
<center>
<h2>Advertising Terms and Conditions</h2>
<br>
These terms and conditions regulate the rights and responsibilities of <b>'.$site_name.'</b> and <b>You</b> in relation to the design and incorporation of your advertising banner and, if applicable, the hosting of a link to your Website on the '.$site_url.' Site.
<br><br>
<h3>Contract Commencement & Duration</h3>
The Advertising Contract will come into force on the Commencement Date and will remain in force until it is terminated in accordance with Clause 5.
<br><br>	
<h3>Payment</h3>
2.1 The cost of the Advertising Services will be the Advertising Charge and the cost of any Design Services, shall be the Design Charge.
<br>
2.2 Payment of the Advertising Charge and, if applicable, the Design Charge, shall be made by you to us either by cheque upon submission of the Application Form or by direct funds transfer made within 7 days of your submission of the Application Form.
<br>
2.3 In consideration of payment of the Advertising Charge, the Advertising Services shall be provided for the Service Period. If you wish the Advertising Services to continue for a further Service Period, we shall invoice you for and you shall pay the Advertising Charge in advance of the next Service Period.
<br>
2.4 Payment may not be made for periods of any less than the Service Period. If you require the provision of Advertising Services for any part of the Service Period, payment of the Advertising Charge must be made in full.
<br>
2.5 For the avoidance of doubt, we will not charge any fee based on the number of links made to the Website through the '.$site_name.' Website.
<br>
2.6 The payment of the Advertising Charge and, if applicable, the Design Charge, must be made in pounds sterling.
<br>
<h3>The Design Services</h3>
3.1 If you make a request on the Application Form for Design Services, we will provide the Design Services upon our receipt of payment of the Design Charge from you.
<br>
3.2 If you require Design Services, you will make the Design Information available to us to allow us to perform the Design Services.
<br>
3.3 We shall be entitled at our discretion to edit and alter the presentation of any Design Information which you may submit.
<br>
3.4 Once we have prepared the Design, we will send you a copy of the Design to you and you will be given the opportunity to make comments on the Design. Any changes made to the Design will be made at our discretion.
<br>
3.5 For the avoidance of doubt, the Advertising Services will not commence until the Design Services are complete.
<br>
<h3>The Advertising Services</h3>
4.1 You shall provide the Content to us in the form prescribed in the Application Form to enable us to provide the Advertising Services.
<br>
4.2 Where you submit Content, or authorise the submission of Content to us, that submission shall be deemed by '.$site_name.' to be a request by you for the publication and use of that Content for the provision of the Advertising Services.
<br>
4.3 We reserve the right at any time to edit or alter the presentation of the Content.
<br>
4.4 For the avoidance of doubt, the Advertising Services shall not include the tracking and logging of user transactions made involving the Website.
<br>
<h3>Termination of the Advertising Contract</h3>
5.1 If, after expiry of any Service Period, payment of a further Advertising Charge is not made, the Advertising Contract and the provision of the Advertising Services shall automatically terminate.
<br>
5.2 We reserve the right to terminate the Advertising Contract and remove the Content and/or Design from the '.$site_url.' Site for any reason.
<br>
5.3 Upon termination in accordance with Clause 5.2 we shall refund you the proportion of the Advertising Charge applicable to any Service Period for which you will no longer receive the Advertising Services.
<br>
5.4 We reserve the right to terminate the Advertising Contract and remove the Content and/or Design from the '.$site_url.' Site without providing a refund of the Advertising Charge to you if, in our reasonable opinion, your conduct, the Content, the Design Information or the Website has brought or would bring us or any other customer of ours into disrepute if we continued to provide the Advertising Services.
<br>
5.5 For the avoidance of doubt, the Design Charge is non-refundable upon termination of the Advertising Contract.
<br>
<h3>Intellectual Property</h3>
6.1 You hereby grant to us a non-exclusive, irrevocable, royalty free, worldwide license to:-
<br>
6.1.1 use the Content and display the Advertisement and the link to the Website for the purpose of the provision of the Advertising Services; and
<br>
6.1.2 use the Design Information for the purpose of the Design Services, if required.
<br>
6.2 You acknowledge that we shall retain all Intellectual Property Rights in the Design. We hereby grant to you an exclusive, royalty free licence to use the Design for the purpose of the display of the Design on the '.$site_name.' Site. The Design may not be used by you for any other purpose.
<br>
6.3 In the event that the Content, the Design Information or the Website infringes the Intellectual Property Rights of a third party, you shall fully indemnify us against any action, claim or proceedings raised by that third party against us for such infringement.
<br>
<h3>Your Relationship with '.$site_name.'</h3>
You acknowledge that the provision of the Advertising Services or the Design Services to you under this Agreement shall not operate or create a joint venture or partnership of any kind between you and us or authorise you or us to act as agent on behalf of the other.
<br>
<h3>Data Protection</h3>
8.1 Please note that when submitting Content, you will be deemed to consent to us:
<br>
8.1.1 holding the Content and making such Content available to the Scottish Tourist Board, Area Tourist Boards, consumers and any of our or their agents or subcontractors; and
<br>
8.1.2 holding and using the information you submit to us to send you details of selected tourism businesses, products and services that we think you will find valuable. We may also occasionally pass your details to selected third parties involved in the tourism industry. If you would prefer your information not to be used in that way, please contact us, giving your name and address: (i) by post to '.$site_name.' '.$site_mail_address.'; or (ii) by email to '.$site_email.'; or (iii) by telephone on 0845 22 55 121.
<br>
8.2 We may record telephone calls between you and the Contact Centre in relation to the formation and/or performance of the Advertising Contract, and you will be deemed to consent to such recording by communicating with the Contact Centre by telephone.
<br>
<h3>Liability</h3>
9.1 Subject to Clause 9.4, in no event shall we be liable to you, in contract, delict (including negligence or breach of statutory duty) or otherwise howsoever and whatever the cause thereof for any loss of profit, business, revenues or anticipated savings, increased costs, special, indirect or consequential damage or loss.
<br>
9.2 Our total liability to you in respect of any costs or losses directly associated with any Advertising Contract with you shall in no circumstances exceed the greater of £100 or 25% of the total Service Charges and any Design Services (as appropriate) paid by you for the Advertising Services and any Design Services (as appropriate) in the Service Period in which the relevant claim arose.
<br>
9.3 You acknowledge and accept that computer and telecommunications systems are not fault free and may from time to time require periods of downtime (being periods during which the '.$site_url.' Site is not available to consumers) for the purposes of repair, maintenance and upgrading. Accordingly, we do not guarantee uninterrupted availability of the '.$site_url.' Site. You accept that you shall have no claim for breach of contract or otherwise in respect of any such period of unavailability.
<br>
9.4 Nothing in this Clause 9 shall seek to limit or exclude our liability to you for death or personal injury caused by our negligence.
<br>
<h3>Content and Design Information</h3>
10.1 You shall ensure that the Content, the Design Information and the information or services made available through the Website is accurate, up to date and not misleading.
<br>
10.2 You shall ensure that the Content and the Design Information complies with the requirements of all relevant legislation for the time being in force or applicable to the United Kingdom, and the British Code of Advertising Practice and all other codes under the general supervision of the Advertising Standards Authority.
<br>
10.3 We shall have no liability whatsoever for the accuracy of or representations made as part of the Content, the Design Information or any information or services made available through the Website.
<br>
10.4 You shall fully indemnify us against any claims, actions or proceedings made or raised against us in respect of the Content, the Design Information or any information or services made available through the Website.
<br>
<h3>General</h3>
11.1 Any notice or other communication to be given in respect of the Advertising Contract shall be in writing and signed by or on behalf of the party giving it. The notice may be served by: (i) delivery in person; (ii) by post; (iii) by facsimile; or (iv) email, to the address, fax number or email address of the other party. Notices to be made to us shall be to the '.$site_name.' at '.$site_mail_address.' or such contact details as may be notified to you by us.
<br>
11.2 We shall be entitled at any time to issue you with contractual terms replacing the terms and conditions set out herein. Following the date of such issue, these terms and conditions shall cease to have effect.
<br>
11.3 We shall be entitled at any time, by giving you prior notice, to alter the Advertising Services or Design Services offered and replace the Advertising Services or Design Services with alternative services.
<br>
11.4 These terms and conditions together with the Application Form constitute the entire agreement between you and us in relation to the Advertising Contract. Nothing in this clause shall exclude the liability of either party for fraud or fraudulent misrepresentation.
<br>
11.5 In the event that the whole or any part of a provision forming part of these terms and conditions may prove to be illegal or unenforceable, the other provisions of these terms and conditions and the remainder of the provision in question shall continue in full force and effect.
<br>
11.6 The construction, validity and performance of the Advertising Contract shall be governed by the law of Texas and the parties submit to the exclusive jurisdiction of the Scottish Courts in as far as permitted by law.
<br>
<h3>Defined Terms</h3>
Unless expressly stated otherwise, the following terms shall have the following meanings: "Advertising Contract" means the contract formed between you and us on the Commencement Date, the terms and conditions of which are set out herein and in the Application Form;"Advertising Services" means the display of your logo and/or picture and sales message on the '.$site_url.' Site, which may also include the hosting of a hyperlink to the Website;"Application Form" means the form submitted by you to us to place an order for the Advertising Services to be provided;"Advertising Charge" means the charge specified in the Application Form for the provision of the Advertising Servcies for one Service Period;"Commencement Date" means the date of our receipt of your payment of the Advertising Charge; "Contact Center" means the facility operated by us for receiving communications from customers requiring our services in Texas; "Content" means your business logo and/or picture and sales message to be advertised on the '.$site_name.' Site; "Design" means the design created by us as a result of our provision of the Design Services; "Design Charge" means the charge specified in the Application Form for our provision of the Design Services; "Design Information" means all information which we require relating to your business and desired advertising message in order to perform the Design Services, as such information is specified in the Application Form; "Design Services" means the optional banner design services offered by us, as described in the Application Form; "Intellectual Property Rights" means all existing and future copyright, design rights (whether registered or unregistered), database rights, patents, trade marks (whether registered or unregistered), semi-conductor topography rights, plant varieties rights, internet rights/domains names, know how, confidential information and any and all applications for any of the foregoing; "Service Period" means renewable periods of 30 days, commencing either on the Commencement Date or, where Design Services are to be provided, on the date on which the Design Services are completed by us, and renewable at intervals of 30 days thereafter; "us" and "we" means '.$site_name.'; "'.$site_name.'" is the trading name of '.$site_name.', a non-profit organization registered in Texas at '.$site_mail_address.'; "'.$site_url.' Site" means the internet portal operated by '.$site_name.' in order to promote and facilitate our services; "Website" means the website through which you provide information to consumers regarding your services; and "you" or "your" means the Ad Client named in the Application Form on whose behalf the link to the website is hosted by '.$site_name.'.
</center>
</td></tr>
</table>';
return $s;
}
?>
