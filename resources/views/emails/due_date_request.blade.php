<!DOCTYPE html>
<html>
<head>
</head>
<body>

<h2>Request Chưa Xử Lý : {{ $request->name }}</h2>
<h4>Người Tạo request : {{ $request->user->name }}</h4>
<p>Ngày Tạo : {{ $request->created_at }}</p>
<p>Ngày hết hạn : {{ $request->due_date }}</p>

  
</body>
</html>
