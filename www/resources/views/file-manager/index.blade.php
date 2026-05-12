<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>游戏文件管理 - Switch 游戏服务器</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css" />
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; margin: 0; padding: 20px; background: #1a1a2e; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .header h1 { margin: 0; color: #FF2D20; font-size: 1.5rem; }
        .header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .btn { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #FF2D20; color: white; }
        .btn-primary:hover { background: #e02618; }
        .btn-secondary { background: #3a3a5c; color: #e0e0e0; }
        .btn-secondary:hover { background: #4a4a6c; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .breadcrumb { margin-bottom: 15px; padding: 10px; background: #2a2a4a; border-radius: 6px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
        .breadcrumb a { color: #FF2D20; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #888; }
        .file-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
        .file-card { background: #2a2a4a; border-radius: 8px; padding: 15px; transition: all 0.2s; border: 1px solid #3a3a5c; }
        .file-card:hover { border-color: #FF2D20; transform: translateY(-2px); }
        .file-card.folder { border-left: 3px solid #ffc107; }
        .file-card.file { border-left: 3px solid #28a745; }
        .file-info { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
        .file-icon { font-size: 32px; flex-shrink: 0; }
        .file-details { flex: 1; min-width: 0; }
        .file-name { font-weight: 600; word-break: break-all; margin-bottom: 4px; }
        .file-meta { font-size: 12px; color: #888; }
        .file-actions { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 10px; }
        .upload-zone { border: 2px dashed #3a3a5c; border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 20px; transition: all 0.2s; cursor: pointer; }
        .upload-zone:hover, .upload-zone.dragover { border-color: #FF2D20; background: rgba(255, 45, 32, 0.1); }
        .upload-zone input { display: none; }
        .upload-text { color: #888; margin-bottom: 10px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .modal-content { background: #2a2a4a; border-radius: 12px; padding: 25px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; color: #FF2D20; }
        .modal-close { background: none; border: none; color: #888; font-size: 24px; cursor: pointer; padding: 0; line-height: 1; }
        .modal-close:hover { color: #fff; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #ccc; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #3a3a5c; background: #1a1a2e; color: #e0e0e0; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #FF2D20; }
        .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state svg { width: 64px; height: 64px; margin-bottom: 15px; opacity: 0.5; }
        .toast { position: fixed; bottom: 20px; right: 20px; padding: 12px 20px; border-radius: 6px; z-index: 2000; display: none; }
        .toast.success { background: #28a745; color: white; }
        .toast.error { background: #dc3545; color: white; }
        .toast.active { display: block; }
        .loading { opacity: 0.5; pointer-events: none; }
        .introjs-tooltip { font-family: 'Figtree', sans-serif !important; }
        .folder-input { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #3a3a5c; background: #1a1a2e; color: #e0e0e0; font-size: 14px; margin-bottom: 10px; }
        .checkbox-wrapper { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .checkbox-wrapper input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        @media (max-width: 600px) {
            .header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; }
            .btn { flex: 1; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📁 游戏文件管理</h1>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="startTour()">🚀 功能引导</button>
                <a href="/" class="btn btn-secondary">← 返回首页</a>
            </div>
        </div>

        <div class="breadcrumb" id="breadcrumb">
            <a href="#" onclick="navigateTo(''); return false;">游戏目录</a>
        </div>

        <div class="upload-zone" id="uploadZone" data-step="1" data-intro="拖放文件或点击这里上传游戏文件到当前目录">
            <input type="file" id="fileInput" multiple accept=".nsp,.xci,.nsz,.xcz,*">
            <input type="file" id="folderInput" multiple webkitdirectory accept=".zip">
            <div class="upload-text">📤 拖放文件到此处或点击选择</div>
            <div style="font-size: 12px; color: #666;">
                <button class="btn btn-sm btn-primary" onclick="document.getElementById('fileInput').click()">选择文件</button>
                <button class="btn btn-sm btn-secondary" onclick="document.getElementById('folderInput').click()">选择文件夹</button>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <button class="btn btn-sm btn-secondary" onclick="showCreateFolderModal()">📁 新建文件夹</button>
        </div>

        <div id="fileList" class="file-grid"></div>
        <div id="emptyState" class="empty-state" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
            </svg>
            <div>当前目录为空</div>
            <div style="font-size: 12px; margin-top: 5px;">上传一些游戏文件开始使用吧！</div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal" id="createFolderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📁 新建文件夹</h3>
                <button class="modal-close" onclick="closeModal('createFolderModal')">&times;</button>
            </div>
            <div class="form-group">
                <label>文件夹名称</label>
                <input type="text" id="newFolderName" placeholder="输入文件夹名称" onkeydown="if(event.key==='Enter')createFolder()">
            </div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal('createFolderModal')">取消</button>
                <button class="btn btn-primary" onclick="createFolder()">创建</button>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal" id="renameModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✏️ 重命名</h3>
                <button class="modal-close" onclick="closeModal('renameModal')">&times;</button>
            </div>
            <input type="hidden" id="renamePath">
            <input type="hidden" id="renameType">
            <div class="form-group">
                <label>新名称</label>
                <input type="text" id="newName" placeholder="输入新名称" onkeydown="if(event.key==='Enter')doRename()">
            </div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal('renameModal')">取消</button>
                <button class="btn btn-primary" onclick="doRename()">确定</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>⚠️ 确认删除</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <input type="hidden" id="deletePath">
            <input type="hidden" id="deleteType">
            <p id="deleteMessage" style="color: #ccc; margin: 0;">确定要删除吗？此操作无法撤销。</p>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal('deleteModal')">取消</button>
                <button class="btn btn-danger" onclick="doDelete()">删除</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
    <script>
        let currentPath = '';
        let tourStarted = false;

        async function loadFiles(path = '') {
            currentPath = path;
            const container = document.getElementById('fileList');
            container.innerHTML = '<div style="text-align:center;padding:40px;color:#888;">加载中...</div>';

            try {
                const resp = await fetch(`/api/file-manager/api/list?path=${encodeURIComponent(path)}`);
                const data = await resp.json();

                updateBreadcrumb(path);

                if (data.directories.length === 0 && data.files.length === 0) {
                    container.innerHTML = '';
                    document.getElementById('emptyState').style.display = 'block';
                } else {
                    document.getElementById('emptyState').style.display = 'none';
                    container.innerHTML = data.directories.map(dir => createFileCard(dir, 'folder')).join('') +
                                          data.files.map(file => createFileCard(file, 'file')).join('');
                }
            } catch (err) {
                showToast('加载失败: ' + err.message, 'error');
            }
        }

        function createFileCard(item, type) {
            const mtime = new Date(item.mtime * 1000).toLocaleString('zh-CN');
            const size = type === 'file' ? formatSize(item.size) : '-';
            const icon = type === 'folder' ? '📁' : getFileIcon(item.name);
            const dataStep = type === 'folder' ? '3' : '4';

            return `
                <div class="file-card ${type}" data-step="${dataStep}">
                    <div class="file-info">
                        <div class="file-icon">${icon}</div>
                        <div class="file-details">
                            <div class="file-name" title="${item.name}">${item.name}</div>
                            <div class="file-meta">
                                ${type === 'folder' ? '文件夹' : size} · ${mtime}
                            </div>
                        </div>
                    </div>
                    <div class="file-actions">
                        ${type === 'folder' ? `
                            <button class="btn btn-sm btn-secondary" onclick="navigateTo('${item.path}')">打开</button>
                            <button class="btn btn-sm btn-secondary" onclick="downloadFolder('${item.path}')">📦 下载</button>
                        ` : `
                            <button class="btn btn-sm btn-primary" onclick="downloadFile('${item.path}')">⬇️ 下载</button>
                        `}
                        <button class="btn btn-sm btn-secondary" onclick="showRenameModal('${item.path}', '${item.name}', '${type}')">✏️ 重命名</button>
                        <button class="btn btn-sm btn-danger" onclick="showDeleteModal('${item.path}', '${item.name}', '${type}')">🗑️ 删除</button>
                    </div>
                </div>
            `;
        }

        function getFileIcon(name) {
            const ext = name.split('.').pop().toLowerCase();
            const icons = {
                'nsp': '🎮', 'xci': '🎮', 'nsz': '🎮', 'xcz': '🎮',
                'zip': '📦', 'rar': '📦', '7z': '📦',
                'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️',
                'mp4': '🎬', 'mkv': '🎬', 'avi': '🎬',
                'mp3': '🎵', 'wav': '🎵', 'flac': '🎵',
                'pdf': '📄', 'txt': '📄', 'doc': '📄',
            };
            return icons[ext] || '📄';
        }

        function formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function updateBreadcrumb(path) {
            const breadcrumb = document.getElementById('breadcrumb');
            const parts = path ? path.split('/') : [];
            let html = `<a href="#" onclick="navigateTo(''); return false;">游戏目录</a>`;
            let current = '';
            for (let i = 0; i < parts.length; i++) {
                current += (i > 0 ? '/' : '') + parts[i];
                html += ` <span>/</span> <a href="#" onclick="navigateTo('${current}'); return false;">${parts[i]}</a>`;
            }
            breadcrumb.innerHTML = html;
        }

        function navigateTo(path) {
            loadFiles(path);
        }

        // File Upload
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('fileInput');
        const folderInput = document.getElementById('folderInput');

        uploadZone.addEventListener('click', () => fileInput.click());

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        folderInput.addEventListener('change', () => {
            handleFiles(folderInput.files, true);
        });

        async function handleFiles(files, isFolder = false) {
            if (files.length === 0) return;

            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            formData.append('path', currentPath);

            const endpoint = isFolder ? '/api/file-manager/api/upload-folder' : '/api/file-manager/api/upload';

            try {
                const resp = await fetch(endpoint, { method: 'POST', body: formData });
                const data = await resp.json();
                if (data.success) {
                    showToast(`上传成功！已上传 ${data.uploaded.length} 个文件`, 'success');
                    loadFiles(currentPath);
                } else {
                    showToast('上传失败: ' + (data.error || '未知错误'), 'error');
                }
            } catch (err) {
                showToast('上传失败: ' + err.message, 'error');
            }
        }

        // Download
        function downloadFile(path) {
            window.location.href = `/api/file-manager/api/download?path=${encodeURIComponent(path)}`;
        }

        function downloadFolder(path) {
            window.location.href = `/api/file-manager/api/download-folder?path=${encodeURIComponent(path)}`;
        }

        // Create Folder
        function showCreateFolderModal() {
            document.getElementById('newFolderName').value = '';
            document.getElementById('createFolderModal').classList.add('active');
            document.getElementById('newFolderName').focus();
        }

        async function createFolder() {
            const name = document.getElementById('newFolderName').value.trim();
            if (!name) return;

            try {
                const formData = new FormData();
                formData.append('path', currentPath);
                formData.append('name', name);

                const resp = await fetch('/api/file-manager/api/create-folder', { method: 'POST', body: formData });
                const data = await resp.json();

                if (data.success) {
                    showToast('文件夹创建成功', 'success');
                    closeModal('createFolderModal');
                    loadFiles(currentPath);
                } else {
                    showToast(data.error || '创建失败', 'error');
                }
            } catch (err) {
                showToast('创建失败: ' + err.message, 'error');
            }
        }

        // Rename
        function showRenameModal(path, name, type) {
            document.getElementById('renamePath').value = path;
            document.getElementById('renameType').value = type;
            document.getElementById('newName').value = name;
            document.getElementById('renameModal').classList.add('active');
            document.getElementById('newName').focus();
            document.getElementById('newName').select();
        }

        async function doRename() {
            const path = document.getElementById('renamePath').value;
            const newName = document.getElementById('newName').value.trim();
            const type = document.getElementById('renameType').value;
            if (!newName) return;

            try {
                const formData = new FormData();
                formData.append('path', path);
                formData.append('newName', newName);
                formData.append('type', type);

                const resp = await fetch('/api/file-manager/api/rename', { method: 'POST', body: formData });
                const data = await resp.json();

                if (data.success) {
                    showToast('重命名成功', 'success');
                    closeModal('renameModal');
                    loadFiles(currentPath);
                } else {
                    showToast(data.error || '重命名失败', 'error');
                }
            } catch (err) {
                showToast('重命名失败: ' + err.message, 'error');
            }
        }

        // Delete
        function showDeleteModal(path, name, type) {
            document.getElementById('deletePath').value = path;
            document.getElementById('deleteType').value = type;
            document.getElementById('deleteMessage').textContent =
                `确定要删除 ${type === 'folder' ? '文件夹' : '文件'} "${name}" 吗？此操作无法撤销。`;
            document.getElementById('deleteModal').classList.add('active');
        }

        async function doDelete() {
            const path = document.getElementById('deletePath').value;
            const type = document.getElementById('deleteType').value;

            try {
                const formData = new FormData();
                formData.append('path', path);
                formData.append('type', type);

                const resp = await fetch('/api/file-manager/api/delete', { method: 'POST', body: formData });
                const data = await resp.json();

                if (data.success) {
                    showToast('删除成功', 'success');
                    closeModal('deleteModal');
                    loadFiles(currentPath);
                } else {
                    showToast(data.error || '删除失败', 'error');
                }
            } catch (err) {
                showToast('删除失败: ' + err.message, 'error');
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal.id);
            });
        });

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} active`;
            setTimeout(() => toast.classList.remove('active'), 3000);
        }

        // Guided Tour
        function startTour() {
            const intro = introJs();
            intro.setOptions({
                steps: [
                    {
                        element: '#uploadZone',
                        intro: '拖放文件到此处或点击选择文件上传。你也可以上传整个文件夹（作为 ZIP 包）。',
                        position: 'bottom'
                    },
                    {
                        element: '.breadcrumb',
                        intro: '使用面包屑导航在文件夹之间切换，点击任意层级可以快速跳转。',
                        position: 'bottom'
                    },
                    {
                        element: '.file-card.folder',
                        intro: '游戏文件夹：点击"打开"进入文件夹，"下载"可以将整个文件夹打包为 ZIP 下载。',
                        position: 'top'
                    },
                    {
                        element: '.file-card.file',
                        intro: '游戏文件：点击"下载"可以直接下载单个游戏文件。',
                        position: 'top'
                    },
                    {
                        element: '.btn.btn-sm.btn-secondary',
                        intro: '新建文件夹可以帮助你整理游戏。使用"重命名"和"删除"按钮管理文件和文件夹。',
                        position: 'top'
                    }
                ],
                showBullets: true,
                showProgress: true,
                nextLabel: '下一步 →',
                prevLabel: '← 上一步',
                doneLabel: '完成'
            });
            intro.start();
        }

        // Initial load
        loadFiles('');
    </script>
</body>
</html>
