<?php
/*
    FICHEIRO: admin_send_driver_whatsapp.php

    OBJETIVO:
    Redirecionar para a versão organizada dentro da pasta admin/.

    Mantive este ficheiro na raiz para compatibilidade com ligações antigas
    e para facilitar a correção caso o professor tente abrir admin_send_driver_whatsapp.php diretamente.
*/
$query = $_SERVER["QUERY_STRING"] ?? "";
$destino = "admin/admin_send_driver_whatsapp.php" . ($query !== "" ? "?" . $query : "");
header("Location: " . $destino);
exit;
?>
