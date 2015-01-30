<?php
# STYLE {{{

# The $STYLE configuration variable can be used to override CSS for the main
# page of phprxy.  Likewise, the $STYLE_URL_FORM configuration variable can
# be used to override CSS for the URL form.  EVERY entry is looped through and
# added as if it were raw CSS.  This is free-form and can do whatever you want,
# so below are only the default values of this variable.  You can add as many
# CSS entries as you'd like.

global $STYLE;
$STYLE=array();

# body of whole document
$STYLE['body']='
	font-family: bitstream vera sans, arial;
	margin: 0px;
	padding: 0px;
';

# <form>
$STYLE['form#proxy_form']='
	margin: 0px;
	padding: 0px;
';

# <table>
$STYLE['table#proxy_table']='
	margin: 0px;
	padding: 0px;
	margin-left: auto;
	margin-right: auto;
';

# the title text above form
$STYLE['td#proxy_title']='
	font-weight: bold;
	font-size: 1.4em;
	text-align: center;
';

# class for all text fields
$STYLE['input.proxy_text']='
	width: 100%;
	border: 1px solid #000000;
';

# class for all select fields
$STYLE['select.proxy_select']='
	width: 100%;
	border: 1px solid #000000;
';

# class for all proxy defined links
$STYLE['a.proxy_link']='
	color: #000000;
';

# class for all submit buttons
$STYLE['input.proxy_submit']='
	border: 1px solid #000000;
	background-color: #FFFFFF;
';

# the simple submit button
$STYLE['input#proxy_submit_simple']='';

# the main submit button
$STYLE['input#proxy_submit_main']='
	width: 100%;
';

# the tunnel proxy ip field
$STYLE['input#proxy_tunnel_ip']='
	float: left;
	width: 73%;
';

# the tunnel proxy port field
$STYLE['input#proxy_tunnel_port']='
	float: right;
	width: 23%;
';

# the link for script information and a link to the author
$STYLE['a#proxy_link_author']='
	float: left;
';

# the link for toggling modes
$STYLE['a#proxy_link_mode']='
	float: right;
';

# }}}

# STYLE_URL_FORM {{{

# The default value for $STYLE_URL_FORM is to be completely blank.  Add entries
# as you please.

global $STYLE_URL_FORM;
$STYLE_URL_FORM=array();

# }}}