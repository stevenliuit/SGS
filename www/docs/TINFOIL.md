# Tinfoil 配置指南

通过 Tinfoil 在 Nintendo Switch 上配置使用 SGS。

## 前置要求

- 已刷 CFW 的 Nintendo Switch
- 已安装 Tinfoil
- SGS 服务器正在运行
- SGS 与 Nintendo Switch 处于同一网络

## 配置步骤

1. **获取服务器 IP** - 在 Linux 上运行 `ip addr show`，Windows 上运行 `ipconfig`，macOS 上运行 `ifconfig`
2. **打开 Tinfoil** - 在 Switch 上启动 Tinfoil
![打开 Tinfoil](images/tinfoil/open-tinfoil.jpg)

3. **添加数据源** - 进入文件浏览器 → 按 ➖ 按钮添加新的数据源
4. **配置参数**：
   - 协议：`http`
   - 主机：`你的服务器IP`
   - 端口：`8030`
   - 路径：`/` 或 `/api/tinfoil`
   - 标题：`Switch 游戏服务器`
   - 启用：`是`
![添加新数据源](images/tinfoil/add-new.jpg)
5. **保存**：按 X 键保存

6. **成功**：你应该看到成功提示信息，内容包含 `Switch 游戏服务器`
![成功](images/tinfoil/success.jpg)

7. **浏览游戏**：根据你放在 `games` 目录中的内容，游戏应会出现在「新游戏」、「新 DLC」或「新更新」中
![游戏列表](images/tinfoil/new-games.jpg)

## 故障排除

- **连接失败**：检查网络、防火墙、服务器是否运行
- **没有游戏**：确认游戏文件在 `games/` 文件夹中且扩展名正确

## API 格式

```json
{
  "files": [
    {"url": "http://你的IP:8030/games/example1.nsz", "size": 123456},
    {"url": "http://你的IP:8030/games/example2.nsz", "size": 123456}
  ],
  "success": "Switch 游戏服务器"
}
```
