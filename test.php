<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        th, td {
            border: 1px, solid, black;
            border-collapse: collapse;
        }
        .noBorder {
            border: none;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>Test1</th><th>Test2</th><th class='noBorder'>Test3</th>
        </tr>
        <tr>
            <td>Test1</td><td>Test2</td><td>Test3</td>
        </tr>
        <tr style='white-space: pre-line'>
            <td>Test1</td><td>Test2</td><td class='noBorder'>Test3</td>
        </tr>
    </table>
</body>
</html>