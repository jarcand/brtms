<?php

require_once dirname(__FILE__) . '/l/config.inc.php';

echo sPrintF('
<html>
<head>
</head>
<body>
<script type="text/javascript">
document.location = \'%1$s/tournaments#tournament/%2$s\';
</script>
</body>
</html>
', $config['ROOT'], @$_GET['tourid']);

