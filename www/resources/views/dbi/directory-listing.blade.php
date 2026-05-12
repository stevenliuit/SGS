<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
    <title>{{ $displayPath }} - 目录索引</title>
</head>
<body>
<h1>{{ $displayPath }} - 目录索引</h1>
<table>
    <tr>
        <th valign="top">&nbsp;</th>
        <th>名称</th>
        <th>修改时间</th>
        <th>大小</th>
        <th>&nbsp;</th>
    </tr>
    <tr><th colspan="5"><hr></th></tr>

    @if($parentUrl)
        <tr>
            <td valign="top">&nbsp;</td>
            <td><a href="{{ $parentUrl }}">上级目录</a></td>
            <td>&nbsp;</td>
            <td align="right">  - </td>
            <td>&nbsp;</td>
        </tr>
    @endif

    @foreach($directories as $directory)
        <tr>
            <td valign="top">&nbsp;</td>
            @if($isDBI)
                <td><a href="{{ rawurlencode($directory['name']) }}/">{{ e($directory['name']) }}/</a></td>
            @else
                <td><a href="{{ $baseUrl }}/{{ $currentPath ? $currentPath . '/' : '' }}{{ rawurlencode($directory['name']) }}">{{ e($directory['name']) }}/</a></td>
            @endif
            <td align="right">{{ date('Y-m-d H:i', $directory['mtime']) }}  </td>
            <td align="right">  - </td>
            <td>&nbsp;</td>
        </tr>
    @endforeach

    @foreach($files as $file)
        <tr>
            <td valign="top">&nbsp;</td>
            @if($isDBI)
                <td><a href="{{ rawurlencode($file['name']) }}">{{ e($file['name']) }}</a></td>
            @else
                <td><a href="{{ $baseUrl }}/{{ $currentPath ? $currentPath . '/' : '' }}{{ rawurlencode($file['name']) }}">{{ e($file['name']) }}</a></td>
            @endif
            <td align="right">{{ date('Y-m-d H:i', $file['mtime']) }}  </td>
            <td align="right">{{ \App\Helpers\FileSize::format($file['size']) }}</td>
            <td>&nbsp;</td>
        </tr>
    @endforeach

    <tr><th colspan="5"><hr></th></tr>
</table>
</body>
</html>
