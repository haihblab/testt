<!DOCTYPE html>
<html>
<head>
    <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }

        tr:nth-child(even) {
          background-color: #dddddd;
        }
    </style>
</head>
<body>

<h2>Danh mục: {{ $category }}</h2>

<table>
      @foreach($content as $key => $value)
        <tr>
            <th>{{ $key }}</th>
            <th>{{ $value }}</th>
        </tr>
    @endforeach
</table>

</body>
</html>
