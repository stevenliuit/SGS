# Switch 游戏服务器 (中文版)

<p align="center">
  <img src="www/public/sgs-icon.png" alt="SGS Logo" width="128">
</p>

Switch 游戏服务器（SGS）是一个基于 Laravel 框架的轻量级应用，通过网络共享你的 Nintendo Switch 游戏文件，让 Tinfoil、DBI 等自制程序能够直接访问和安装游戏。

使用 SGS，你无需将 Switch 连接到电脑，即可通过无线网络安装游戏。设置完成后，同一网络下的所有 Nintendo Switch 设备都可以直接从服务器下载和安装游戏。

## 功能特性

- 🎮 **网络文件共享** - 通过网络共享 Switch 游戏文件（.nsz、.nsp、.xci、.xcz）
- 📱 **Tinfoil 支持** - 为 Tinfoil 应用提供 JSON 格式的游戏索引
- 🗂️ **DBI 支持** - 为 DBI 应用提供 Apache 风格的目录列表
- 📁 **文件管理** - 网页端管理游戏文件（上传、下载、删除、重命名）
- 🐳 **Docker 部署** - 使用 Docker Compose 完全容器化部署
- ⚡ **自动配置** - 首次运行时自动安装依赖

## 快速开始

### 环境要求

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

### 安装步骤

1. **克隆仓库**
   ```bash
   git clone https://github.com/stevenliuit/SGS
   cd SGS
   ```

2. **启动应用**
   ```bash
   docker-compose up -d
   ```

   应用会自动完成以下操作：
   - 安装 Composer 依赖
   - 安装并构建前端资源
   - 创建存储目录软链接
   - 启动 Web 服务器

3. **访问应用**

   在浏览器中打开：
   ```
   http://localhost:8030
   ```

   如果从其他设备访问，请将 `localhost` 替换为服务器 IP 地址。

## 添加游戏

将你的 Nintendo Switch 游戏文件放入 `games/` 目录。支持的文件格式：

- `.nsz` - 压缩版 NSP
- `.nsp` - Nintendo 提交包
- `.xci` - NX 卡镜像
- `.xcz` - 压缩版 XCI

## 客户端配置

### Tinfoil 配置教程

详细说明请访问 `http://YOUR_SERVER_IP:8030/docs/TINFOIL` 或查看 [www/docs/TINFOIL.md](www/docs/TINFOIL.md)。

### DBI 配置教程

详细说明请访问 `http://YOUR_SERVER_IP:8030/docs/DBI` 或查看 [www/docs/DBI.md](www/docs/DBI.md)。

## API 接口

- **Tinfoil 接口**: `http://YOUR_SERVER_IP:8030/api/tinfoil`
- **DBI 接口**: `http://YOUR_SERVER_IP:8030/api/dbi`
- **通用接口**（适用于 Tinfoil 和 DBI）: `http://YOUR_SERVER_IP:8030`

## 文件管理

SGS 提供网页端文件管理功能，方便你管理游戏文件：

访问地址：`http://YOUR_SERVER_IP:8030/manage`

功能包括：
- 📤 **上传文件** - 支持拖放或点击上传游戏文件
- 📦 **上传文件夹** - 支持上传 ZIP 包自动解压
- ⬇️ **下载文件** - 直接下载单个游戏文件
- 📥 **下载文件夹** - 将整个游戏文件夹打包为 ZIP 下载
- 🗑️ **删除** - 支持文件和文件夹删除（带确认提示）
- ✏️ **重命名** - 支持文件和文件夹重命名
- 📁 **新建文件夹** - 在当前目录创建新文件夹
- 🚀 **引导功能** - 首次使用可点击"功能引导"查看使用教程

## 法律声明

⚠️ **重要提示**：本软件仅供个人使用。它旨在帮助你管理和访问通过合法途径备份的游戏文件。

- 仅使用你合法拥有的游戏配合本软件
- 盗版是违法行为，侵犯著作权
- 本软件仅供学习和备份目的
- 开发者不支持任何形式的盗版行为
- 用户有责任确保其使用符合当地法律法规

使用本软件即表示你同意遵守所有适用法律和法规。

## 许可证

本作品采用 [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/) 许可证。

![CC](https://mirrors.creativecommons.org/presskit/icons/cc.svg)![BY](https://mirrors.creativecommons.org/presskit/icons/by.svg)![NC](https://mirrors.creativecommons.org/presskit/icons/nc.svg)![SA](https://mirrors.creativecommons.org/presskit/icons/sa.svg)
