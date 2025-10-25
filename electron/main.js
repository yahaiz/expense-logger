const { app, BrowserWindow, Menu, ipcMain, dialog } = require('electron');
const path = require('path');
const { spawn } = require('child_process');
const fs = require('fs');
const os = require('os');

let mainWindow;
let splashWindow;
let phpServer;
let serverPort = 3000;

// Get the application data directory for user-specific data
const userDataPath = path.join(os.homedir(), 'ExpenseLogger');
const dbPath = path.join(userDataPath, 'expenses.db');

// Ensure user data directory exists
if (!fs.existsSync(userDataPath)) {
    fs.mkdirSync(userDataPath, { recursive: true });
}

// Copy initial database if it doesn't exist
const initialDbPath = path.join(__dirname, '..', 'data', 'expenses.db');
if (!fs.existsSync(dbPath) && fs.existsSync(initialDbPath)) {
    fs.copyFileSync(initialDbPath, dbPath);
}

function startPHPServer() {
    return new Promise((resolve, reject) => {
        // Find PHP executable
        const phpPaths = [
            path.join(process.resourcesPath, 'app', 'php', 'php.exe'), // Packaged PHP (ASAR disabled)
            path.join(__dirname, '..', 'php', 'php.exe'), // Development path
            'C:\\xampp\\php\\php.exe',
            'C:\\php\\php.exe',
            'php.exe'
        ];

        let phpPath = null;
        for (const testPath of phpPaths) {
            if (fs.existsSync(testPath)) {
                phpPath = testPath;
                break;
            }
        }

        if (!phpPath) {
            reject(new Error('PHP executable not found. Please install PHP.'));
            return;
        }

        // Start PHP built-in server
        const appPath = path.join(__dirname, '..');
        const sessionPath = path.join(userDataPath, 'sessions');
        
        // Ensure sessions directory exists
        if (!fs.existsSync(sessionPath)) {
            fs.mkdirSync(sessionPath, { recursive: true });
        }
        
        phpServer = spawn(phpPath, [
            '-d', `session.save_path=${sessionPath}`,
            '-S', `localhost:${serverPort}`, 
            '-t', appPath
        ], {
            cwd: appPath,
            stdio: ['pipe', 'pipe', 'pipe'],
            env: {
                ...process.env,
                EXPENSELOGGER_USER_DATA: userDataPath,
                EXPENSELOGGER_DB_PATH: dbPath
            }
        });

        phpServer.stdout.on('data', (data) => {
            console.log(`PHP Server: ${data}`);
            if (data.toString().includes('Development Server started')) {
                resolve();
            }
        });

        phpServer.stderr.on('data', (data) => {
            console.error(`PHP Server Error: ${data}`);
        });

        phpServer.on('close', (code) => {
            console.log(`PHP server exited with code ${code}`);
        });

        // Give server time to start
        setTimeout(() => {
            resolve();
        }, 2000);
    });
}

function createSplashWindow() {
    splashWindow = new BrowserWindow({
        width: 500,
        height: 300,
        transparent: true,
        frame: false,
        alwaysOnTop: true,
        skipTaskbar: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    // Load the splash screen HTML
    splashWindow.loadFile(path.join(__dirname, '..', 'splash.html'));
    splashWindow.center();
}

function createWindow() {
    // Create the browser window
    mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            enableRemoteModule: false,
            preload: path.join(__dirname, 'preload.js')
        },
        title: 'ExpenseLogger',
        show: false
    });

    // Load the PHP application
    mainWindow.loadURL(`http://localhost:${serverPort}/index.php`);

    // Show window when ready to prevent visual flash and close splash screen
    mainWindow.once('ready-to-show', () => {
        // Close the splash screen
        if (splashWindow) {
            splashWindow.close();
            splashWindow = null;
        }
        // Show the main application window
        mainWindow.show();
    });

    // Emitted when the window is closed
    mainWindow.on('closed', () => {
        mainWindow = null;
        if (phpServer) {
            phpServer.kill();
        }
    });

    // Hide menu bar in production
    if (process.env.NODE_ENV === 'production') {
        mainWindow.setMenuBarVisibility(false);
    }
    
    // Always hide menu bar for cleaner UI
    mainWindow.setMenuBarVisibility(false);
}

// This method will be called when Electron has finished initialization
app.whenReady().then(async () => {
    try {
        await startPHPServer();
        createSplashWindow();
        createWindow();

        // On macOS, re-create window when dock icon is clicked
        app.on('activate', () => {
            if (BrowserWindow.getAllWindows().length === 0) {
                createWindow();
            }
        });
    } catch (error) {
        dialog.showErrorBox('Error', `Failed to start ExpenseLogger: ${error.message}`);
        app.quit();
    }
});

// Quit when all windows are closed, except on macOS
app.on('window-all-closed', () => {
    if (phpServer) {
        phpServer.kill();
    }
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

// Handle app shutdown
app.on('before-quit', () => {
    if (phpServer) {
        phpServer.kill();
    }
});

// IPC handlers for communication with renderer
ipcMain.handle('get-user-data-path', () => {
    return userDataPath;
});

ipcMain.handle('get-db-path', () => {
    return dbPath;
});