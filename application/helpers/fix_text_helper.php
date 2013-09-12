<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function cleanup_chars($text)
{
	$search[0] = "<";
	$replace[0] = "&lt;";
	
	$search[1] = ">";
	$replace[1] = "&gt;";
	
	$search[2] = "\"";
	$replace[2] = "&quot;";
	
	$search[3] = "'";
	$replace[3] = "&apos;";
	
	$search[4] = "-";
	$replace[4] = "&#45;";

	return str_replace($search, $replace, $text);
}

function special_char_cleanup($text)
{
	$search[0] = "&acirc;&#25;";
	$replace[0] = "'";
	
	$search[1] = "&Acirc;";
	$replace[1] = "";
	
	$search[2] = "&acirc;&#24;";
	$replace[2] = "'";
	
	$search[3] = '&acirc;&#19;';
	$replace[3] = '-';
	
	return str_replace($search, $replace, $text);
}

function cleanup_special_chars($text)
{
	$search[0] = '&#8230;';
	$replace[0] = '...';
	
	$search[1] = '&#8216;';
	$replace[1] = "'";
	
	$search[2] = '&#8217;';
	$replace[2] = "'";
	
	$search[3] = '&#8220;';
	$replace[3] = '"';
	
	$search[4] = '&#8221;';
	$replace[4] = '"';
	
	$search[5] = '&#161;';
	$replace[5] = '&iexcl;';
	
	$search[6] = '&#162;';
	$replace[6] = '&cent;';
	
	$search[7] = '&#163;';
	$replace[7] = '&pound;';
	
	$search[8] = '&#164;';
	$replace[8] = '&curren;';
	
	$search[9] = '&#165;';
	$replace[9] = '&yen;';
	
	$search[10] = '&#166;';
	$replace[10] = '&brvbar;';
	
	$search[11] = '&#167;';
	$replace[11] = '&sect;';
	
	$search[12] = '&#168;';
	$replace[12] = '&uml;';
	
	$search[13] = '&#169;';
	$replace[13] = '&copy;';
	
	$search[14] = '&#170;';
	$replace[14] = '&ordf;';
	
	$search[15] = '&#171;';
	$replace[15] = '&laquo;';
	
	$search[16] = '&#172;';
	$replace[16] = '&not;';
	
	$search[17] = '&#173;';
	$replace[17] = '&shy;';
	
	$search[18] = '&#174;';
	$replace[18] = '&reg;';
	
	$search[19] = '&#175;';
	$replace[19] = '&macr;';
	
	$search[20] = '&#176;';
	$replace[20] = '&deg;';
	
	$search[21] = '&#177;';
	$replace[21] = '&plusmn;';
	
	$search[22] = '&#178;';
	$replace[22] = '&sup2;';
	
	$search[23] = '&#179;';
	$replace[23] = '&sup3;';
	
	$search[24] = '&#180;';
	$replace[24] = '&acute;';
	
	$search[25] = '&#181;';
	$replace[25] = '&micro;';
	
	$search[26] = '&#182;';
	$replace[26] = '&para;';
	
	$search[27] = '&#183;';
	$replace[27] = '&middot;';
	
	$search[28] = '&#184;';
	$replace[28] = '&cedil;';
	
	$search[29] = '&#185;';
	$replace[29] = '&sup1;';
	
	$search[30] = '&#186;';
	$replace[30] = '&ordm;';
	
	$search[31] = '&#187;';
	$replace[31] = '&raquo;';
	
	$search[32] = '&#188;';
	$replace[32] = '&frac14;';
	
	$search[33] = '&#189;';
	$replace[33] = '&frac12;';
	
	$search[34] = '&#190;';
	$replace[34] = '&frac34;';
	
	$search[35] = '&#191;';
	$replace[35] = '&iquest;';
	
	$search[36] = '&#215;';
	$replace[36] = '&times;';
	
	$search[37] = '&#247;';
	$replace[37] = '&divide;';
	
	$search[38] = '&#192;';
	$replace[38] = '&Agrave;';
	
	$search[39] = '&#193;';
	$replace[39] = '&Aacute;';
	
	$search[40] = '&#194;';
	$replace[40] = '&Acirc;';
	
	$search[41] = '&#195;';
	$replace[41] = '&Atilde;';
	
	$search[42] = '&#196;';
	$replace[42] = '&Auml;';
	
	$search[43] = '&#197;';
	$replace[43] = '&Aring;';
	
	$search[44] = '&#198;';
	$replace[44] = '&AElig;';
	
	$search[45] = '&#199;';
	$replace[45] = '&Ccedil;';
	
	$search[46] = '&#200;';
	$replace[46] = '&Egrave;';
	
	$search[47] = '&#201;';
	$replace[47] = '&Eacute;';
	
	$search[48] = '&#202;';
	$replace[48] = '&Ecirc;';
	
	$search[49] = '&#203;';
	$replace[49] = '&Euml;';
	
	$search[50] = '&#204;';
	$replace[50] = '&Igrave;';
	
	$search[51] = '&#205;';
	$replace[51] = '&Iacute;';
	
	$search[52] = '&#206;';
	$replace[52] = '&Icirc;';
	
	$search[53] = '&#207;';
	$replace[53] = '&Iuml;';
	
	$search[54] = '&#208;';
	$replace[54] = '&ETH;';
	
	$search[55] = '&#209;';
	$replace[55] = '&Ntilde;';
	
	$search[56] = '&#210;';
	$replace[56] = '&Ograve;';
	
	$search[57] = '&#211;';
	$replace[57] = '&Oacute;';
	
	$search[58] = '&#212;';
	$replace[58] = '&Ocirc;';
	
	$search[59] = '&#213;';
	$replace[59] = '&Otilde;';
	
	$search[60] = '&#214;';
	$replace[60] = '&Ouml;';
	
	$search[61] = '&#216;';
	$replace[61] = '&Oslash;';
	
	$search[62] = '&#217;';
	$replace[62] = '&Ugrave;';
	
	$search[63] = '&#218;';
	$replace[63] = '&Uacute;';
	
	$search[64] = '&#219;';
	$replace[64] = '&Ucirc;';
	
	$search[65] = '&#220;';
	$replace[65] = '&Uuml;';
	
	$search[66] = '&#221;';
	$replace[66] = '&Yacute;';
	
	$search[67] = '&#222;';
	$replace[67] = '&THORN;';
	
	$search[68] = '&#223;';
	$replace[68] = '&szlig;';
	
	$search[69] = '&#224;';
	$replace[69] = '&agrave;';
	
	$search[70] = '&#225;';
	$replace[70] = '&aacute;';
	
	$search[71] = '&#226;';
	$replace[71] = '&acirc;';
	
	$search[72] = '&#227;';
	$replace[72] = '&atilde;';
	
	$search[73] = '&#228;';
	$replace[73] = '&auml;';
	
	$search[74] = '&#229;';
	$replace[74] = '&aring;';
	
	$search[75] = '&#230;';
	$replace[75] = '&aelig;';
	
	$search[76] = '&#231;';
	$replace[76] = '&ccedil;';
	
	$search[77] = '&#232;';
	$replace[77] = '&egrave;';
	
	$search[78] = '&#233;';
	$replace[78] = '&eacute;';
	
	$search[79] = '&#234;';
	$replace[79] = '&ecirc;';
	
	$search[80] = '&#235;';
	$replace[80] = '&euml;';
	
	$search[81] = '&#236;';
	$replace[81] = '&igrave;';
	
	$search[82] = '&#237;';
	$replace[82] = '&iacute;';
	
	$search[83] = '&#238;';
	$replace[83] = '&icirc;';
	
	$search[84] = '&#239;';
	$replace[84] = '&iuml;';
	
	$search[85] = '&#240;';
	$replace[85] = '&eth;';
	
	$search[86] = '&#241;';
	$replace[86] = '&ntilde;';
	
	$search[87] = '&#242;';
	$replace[87] = '&ograve;';
	
	$search[88] = '&#243;';
	$replace[88] = '&oacute;';
	
	$search[89] = '&#244;';
	$replace[89] = '&ocirc;';
	
	$search[90] = '&#245;';
	$replace[90] = '&otilde;';
	
	$search[91] = '&#246;';
	$replace[91] = '&ouml;';
	
	$search[92] = '&#248;';
	$replace[92] = '&oslash;';
	
	$search[93] = '&#249;';
	$replace[93] = '&ugrave;';
	
	$search[94] = '&#250;';
	$replace[94] = '&uacute;';
	
	$search[95] = '&#251;';
	$replace[95] = '&ucirc;';
	
	$search[96] = '&#252;';
	$replace[96] = '&uuml;';
	
	$search[97] = '&#253;';
	$replace[97] = '&yacute;';
	
	$search[98] = '&#254;';
	$replace[98] = '&thorn;';
	
	$search[99] = '&#255;';
	$replace[99] = '&yuml;';
	
	/**
	 * Math Symboles
	 * 
	$search[100] = '&#8704;';
	$replace[100] = '&forall;';
	
	$search[101] = '&#8706;';
	$replace[101] = '&part;';
	
	$search[102] = '&#8707;';
	$replace[102] = '&exists;';
	
	$search[103] = '&#8709;';
	$replace[103] = '&empty;';
	
	$search[104] = '&#8711;';
	$replace[104] = '&nabla;';
	
	$search[105] = '&#8712;';
	$replace[105] = '&isin;';
	
	$search[106] = '&#8713;';
	$replace[106] = '&notin;';
	
	$search[107] = '&#8715;';
	$replace[107] = '&ni;';
	
	$search[108] = '&#8719;';
	$replace[108] = '&prod;';
	
	$search[109] = '&#8721;';
	$replace[109] = '&sum;';
	
	$search[110] = '&#8722;';
	$replace[110] = '&minus;';
	
	$search[111] = '&#8727;';
	$replace[111] = '&lowast;';
	
	$search[112] = '&#8730;';
	$replace[112] = '&radic;';
	
	$search[113] = '&#8733;';
	$replace[113] = '&prop;';
	
	$search[114] = '&#8734;';
	$replace[114] = '&infin;';
	
	$search[115] = '&#8736;';
	$replace[115] = '&ang;';
	
	$search[116] = '&#8743;';
	$replace[116] = '&and;';
	
	$search[117] = '&#8744;';
	$replace[117] = '&or;';
	
	$search[118] = '&#8745;';
	$replace[118] = '&cap;';
	
	$search[119] = '&#8746;';
	$replace[119] = '&cup;';
	
	$search[120] = '&#8747;';
	$replace[120] = '&int;';
	
	$search[121] = '&#8756;';
	$replace[121] = '&there4;';
	
	$search[122] = '&#8764;';
	$replace[122] = '&sim;';
	
	$search[123] = '&#8773;';
	$replace[123] = '&cong;';
	
	$search[124] = '&#8776;';
	$replace[124] = '&asymp;';
	
	$search[125] = '&#8800;';
	$replace[125] = '&ne;';
	
	$search[126] = '&#8801;';
	$replace[126] = '&equiv;';
	
	$search[127] = '&#8804;';
	$replace[127] = '&le;';
	
	$search[128] = '&#8805;';
	$replace[128] = '&ge;';
	
	$search[129] = '&#8834;';
	$replace[129] = '&sub;';
	
	$search[130] = '&#8835;';
	$replace[130] = '&sup;';
	
	$search[131] = '&#8836;';
	$replace[131] = '&nsub;';
	
	$search[132] = '&#8838;';
	$replace[132] = '&sube;';
	
	$search[133] = '&#8839;';
	$replace[133] = '&supe;';
	
	$search[134] = '&#8853;';
	$replace[134] = '&oplus;';
	
	$search[135] = '&#8855;';
	$replace[135] = '&otimes;';
	
	$search[136] = '&#8869;';
	$replace[136] = '&perp;';
	
	$search[137] = '&#8901;';
	$replace[137] = '&sdot;';
	
	 * end Math Symbols
	 */
	
	/**
	 * Greek Symbols
	 * 
	$search[138] = '&#913;';
	$replace[138] = '&Alpha;';
	
	$search[139] = '&#914;';
	$replace[139] = '&Beta;';
	
	$search[140] = '&#915;';
	$replace[140] = '&Gamma;';
	
	$search[141] = '&#916;';
	$replace[141] = '&Delta;';
	
	$search[142] = '&#917;';
	$replace[142] = '&Epsilon;';
	
	$search[143] = '&#918;';
	$replace[143] = '&Zeta;';
	
	$search[144] = '&#919;';
	$replace[144] = '&Eta;';
	
	$search[145] = '&#920;';
	$replace[145] = '&Theta;';
	
	$search[146] = '&#921;';
	$replace[146] = '&Iota;';
	
	$search[147] = '&#922;';
	$replace[147] = '&Kappa;';
	
	$search[148] = '&#923;';
	$replace[148] = '&Lambda;';
	
	$search[149] = '&#924;';
	$replace[149] = '&Mu;';
	
	$search[150] = '&#925;';
	$replace[150] = '&Nu;';
	
	$search[151] = '&#926;';
	$replace[151] = '&Xi;';
	
	$search[152] = '&#927;';
	$replace[152] = '&Omicron;';
	
	$search[153] = '&#928;';
	$replace[153] = '&Pi;';
	
	$search[154] = '&#929;';
	$replace[154] = '&Rho;';
	
	$search[155] = '&#931;';
	$replace[155] = '&Sigma;';
	
	$search[156] = '&#932;';
	$replace[156] = '&Tau;';
	
	$search[157] = '&#933;';
	$replace[157] = '&Upsilon;';
	
	$search[158] = '&#934;';
	$replace[158] = '&Phi;';
	
	$search[159] = '&#935;';
	$replace[159] = '&Chi;';
	
	$search[160] = '&#936;';
	$replace[160] = '&Psi;';
	
	$search[161] = '&#937;';
	$replace[161] = '&Omega;';
	
	$search[162] = '&#945;';
	$replace[162] = '&alpha;';
	
	$search[163] = '&#946;';
	$replace[163] = '&beta;';
	
	$search[164] = '&#947;';
	$replace[164] = '&gamma;';
	
	$search[165] = '&#948;';
	$replace[165] = '&delta;';
	
	$search[166] = '&#949;';
	$replace[166] = '&epsilon;';
	
	$search[167] = '&#950;';
	$replace[167] = '&zeta;';
	
	$search[168] = '&#951;';
	$replace[168] = '&eta;';
	
	$search[169] = '&#952;';
	$replace[169] = '&theta;';
	
	$search[170] = '&#953;';
	$replace[170] = '&iota;';
	
	$search[171] = '&#954;';
	$replace[171] = '&kappa;';
	
	$search[172] = '&#955;';
	$replace[172] = '&lambda;';
	
	$search[173] = '&#956;';
	$replace[173] = '&mu;';
	
	$search[174] = '&#957;';
	$replace[174] = '&nu;';
	
	$search[175] = '&#958;';
	$replace[175] = '&xi;';
	
	$search[176] = '&#959;';
	$replace[176] = '&omicron;';
	
	$search[177] = '&#960;';
	$replace[177] = '&pi;';
	
	$search[178] = '&#961;';
	$replace[178] = '&rho;';
	
	$search[179] = '&#962;';
	$replace[179] = '&sigmaf;';
	
	$search[180] = '&#963;';
	$replace[180] = '&sigma;';
	
	$search[181] = '&#964;';
	$replace[181] = '&tau;';
	
	$search[182] = '&#965;';
	$replace[182] = '&upsilon;';
	
	$search[183] = '&#966;';
	$replace[183] = '&phi;';
	
	$search[184] = '&#967;';
	$replace[184] = '&chi;';
	
	$search[185] = '&#968;';
	$replace[185] = '&psi;';
	
	$search[186] = '&#969;';
	$replace[186] = '&omega;';
	
	$search[187] = '&#977;';
	$replace[187] = '&thetasym;';
	
	$search[188] = '&#978;';
	$replace[188] = '&upsih;';
	
	$search[189] = '&#982;';
	$replace[189] = '&piv;';
	
	 * end Greek Symbols
	 */
	
	/**
	 *	Other Symbols
	 *	
	$search[190] = '&#338;';
	$replace[190] = '&OElig;';
	
	$search[191] = '&#339;';
	$replace[191] = '&oelig;';
	
	$search[192] = '&#352;';
	$replace[192] = '&Scaron;';
	
	$search[193] = '&#353;';
	$replace[193] = '&scaron;';
	
	$search[194] = '&#376;';
	$replace[194] = '&Yuml;';
	
	$search[195] = '&#402;';
	$replace[195] = '&fnof;';
	
	$search[196] = '&#710;';
	$replace[196] = '&circ;';
	
	$search[197] = '&#732;';
	$replace[197] = '&tilde;';
	
	$search[198] = '&#8194;';
	$replace[198] = '&ensp;';
	
	$search[199] = '&#8195;';
	$replace[199] = '&emsp;';
	
	$search[200] = '&#8201;';
	$replace[200] = '&thinsp;';
	
	$search[201] = '&#8204;';
	$replace[201] = '&zwnj;';
	
	$search[202] = '&#8205;';
	$replace[202] = '&zwj;';
	
	$search[203] = '&#8206;';
	$replace[203] = '&lrm;';
	
	$search[204] = '&#8207;';
	$replace[204] = '&rlm;';
	
	$search[205] = '&#8211;';
	$replace[205] = '&ndash;';
	
	$search[206] = '&#8212;';
	$replace[206] = '&mdash;';
	
	$search[207] = '&#8216;';
	$replace[207] = '&lsquo;';
	
	$search[208] = '&#8217;';
	$replace[208] = '&rsquo;';
	
	$search[209] = '&#8218;';
	$replace[209] = '&sbquo;';
	
	$search[210] = '&#8220;';
	$replace[210] = '&ldquo;';
	
	$search[211] = '&#8221;';
	$replace[211] = '&rdquo;';
	
	$search[212] = '&#8222;';
	$replace[212] = '&bdquo;';
	
	$search[213] = '&#8224;';
	$replace[213] = '&dagger;';
	
	$search[214] = '&#8225;';
	$replace[214] = '&Dagger;';
	

	
	$search[230] = '&#8629;';
	$replace[230] = '&crarr;';
	
	$search[231] = '&#8968;';
	$replace[231] = '&lceil;';
	
	$search[232] = '&#8969;';
	$replace[232] = '&rceil;';
	
	$search[233] = '&#8970;';
	$replace[233] = '&lfloor;';
	
	$search[234] = '&#8971;';
	$replace[234] = '&rfloor;';
	
	$search[235] = '&#9674;';
	$replace[235] = '&loz;';
	
	$search[236] = '&#9824;';
	$replace[236] = '&spades;';
	
	$search[237] = '&#9827;';
	$replace[237] = '&clubs;';
	
	$search[238] = '&#9829;';
	$replace[238] = '&hearts;';
	
	$search[239] = '&#9830;';
	$replace[239] = '&diams;';
	
	 */
	
	$search[215] = '&#8226;';
	$replace[215] = '&bull;';
	
	$search[216] = '&#8230;';
	$replace[216] = '&hellip;';
	
	$search[217] = '&#8240;';
	$replace[217] = '&permil;';
	
	$search[218] = '&#8242;';
	$replace[218] = '&prime;';
	
	$search[219] = '&#8243;';
	$replace[219] = '&Prime;';
	
	$search[220] = '&#8249;';
	$replace[220] = '&lsaquo;';
	
	$search[221] = '&#8250;';
	$replace[221] = '&rsaquo;';
	
	$search[222] = '&#8254;';
	$replace[222] = '&oline;';
	
	$search[223] = '&#8364;';
	$replace[223] = '&euro;';
	
	$search[224] = '&#8482;';
	$replace[224] = '&trade;';
	
	$search[225] = '&#8592;';
	$replace[225] = '&larr;';
	
	$search[226] = '&#8593;';
	$replace[226] = '&uarr;';
	
	$search[227] = '&#8594;';
	$replace[227] = '&rarr;';
	
	$search[228] = '&#8595;';
	$replace[228] = '&darr;';
	
	$search[229] = '&#8596;';
	$replace[229] = '&harr;';
	
	return str_replace($search, $replace, $text);
	
}
