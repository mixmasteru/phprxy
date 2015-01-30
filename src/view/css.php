<?php
# CSS STATIC CONTENT {{{

# CSS MAIN {{{

if(QUERY_STRING=='css_main'){
	header('Content-Type: text/css');
	static_cache();

	foreach($STYLE as $id=>$style){
		echo "{$id} {{$style}}\n\n";
	}

	echo ".display_none { display: none !important; }\n";
	echo ".display_tr { display: table-row !important; }\n";

	exit();
}

# }}}

# CSS URL FRAME {{{

if(QUERY_STRING=='css_url_frame'){
	header('Content-Type: text/css');
	static_cache();

	foreach($STYLE_URL_FORM as $id=>$style){
		echo "{$id} {{$style}}\n\n";
	}

	exit();
}

# }}}

# }}}