<div class="error">
<?php

if (isset($message)) {
	echo $message;
} else if (isset($error)) {
	echo $error;
}
?>
</div>