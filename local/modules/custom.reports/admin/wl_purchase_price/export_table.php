<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        td {
            mso-number-format: \@;
        }

        .number0 {
            mso-number-format: 0;
        }

        .number2 {
            mso-number-format: Fixed;
        }
    </style>
</head>
<body>
<table border="1">
    <tr>
        <? foreach ($xlsColumns as $column): ?>
            <td><?= $column['name'] ?></td>
        <? endforeach; ?>
    </tr>
    <? foreach ($list as $ditem): ?>
        <tr>
            <? foreach ($xlsColumns as $column): ?>
                <td align="<?= $column['align'] ?>"><?= $ditem['data'][$column['id']] ?></td>
            <? endforeach; ?>
        </tr>
    <? endforeach; ?>
</table>
</body>
</html>
