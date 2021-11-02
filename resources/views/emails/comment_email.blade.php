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

<h2>Tên Request: {{ $data['request'] }}</h2>
<h4>Người Comment: {{ $data['user'] }}</h4>
<p>Nội Dung: {{ $data['content'] }}</p>
<p>Ngày Tạo: {{ $data['created_at'] }}</p>

  
</body>
</html>
