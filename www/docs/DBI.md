# DBI 配置指南

通过 DBI 在 Nintendo Switch 上配置使用 SGS。

## 前置要求

- 已刷 CFW 的 Nintendo Switch
- 已安装 DBI
- SGS 服务器正在运行
- SGS 与 Nintendo Switch 处于同一网络

## 配置步骤

### 1. 获取服务器 IP

在 Linux 上运行 `ip addr show`，Windows 上运行 `ipconfig`，macOS 上运行 `ifconfig` 来查找服务器 IP 地址。

### 2. 创建 DBI 定位文件

在 SD 卡上创建或编辑文件 `/switch/dbi/dbi.locations`

添加以下内容：

```ini
[Location_0]
Name=Switch 游戏服务器
Type=ApacheHTTP
URL=http://你的IP:8030/
```

**或者**如果你偏好 API 端点：

```ini
[Location_0]
Name=Switch 游戏服务器
Type=ApacheHTTP
URL=http://你的IP:8030/api/dbi
```

**注意**：如果你已经配置了其他定位点，使用 `[Location_X]`，其中 X 是下一个可用编号（例如，如果你已有 `Location_0` 和 `Location_1`，则使用 `Location_2`）。

### 3. 启动 DBI

从 Switch 主菜单打开 DBI。

### 4. 选择你的服务器

从列表中选择「Switch 游戏服务器」。

### 5. 浏览并安装

浏览文件夹并安装你的游戏！

## 功能特点

- Apache 风格目录列表
- 文件夹导航
- 支持 .nsz、.nsp、.xci、.xcz 格式

## 故障排除

- **连接超时**：检查网络和防火墙
- **文件夹为空**：确认游戏文件在 `games/` 目录中
- **安装失败**：检查 Switch 存储空间
- **服务器未列出**：确认 `dbi.locations` 文件位于正确位置且格式正确
