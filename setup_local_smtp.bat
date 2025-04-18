@echo off
echo Setting up local SMTP server for testing...

:: Check if Python is installed
python --version > nul 2>&1
if %errorlevel% neq 0 (
    echo Python is not installed. Please install Python first.
    exit /b 1
)

:: Install Python packages if needed
pip install aiosmtpd > nul 2>&1
if %errorlevel% neq 0 (
    echo Installing aiosmtpd package...
    pip install aiosmtpd
)

:: Create Python script for SMTP server
echo import sys, asyncio > smtp_server.py
echo from aiosmtpd.controller import Controller >> smtp_server.py
echo from aiosmtpd.handlers import Debugging >> smtp_server.py
echo. >> smtp_server.py
echo class CustomHandler(Debugging): >> smtp_server.py
echo     async def handle_DATA(self, server, session, envelope): >> smtp_server.py
echo         print('Received message from: %s' %% envelope.mail_from) >> smtp_server.py
echo         print('Recipients: %s' %% envelope.rcpt_tos) >> smtp_server.py
echo         print('Message data:') >> smtp_server.py
echo         print(envelope.content.decode('utf8', errors='replace')) >> smtp_server.py
echo         print('End of message') >> smtp_server.py
echo         return '250 Message accepted for delivery' >> smtp_server.py
echo. >> smtp_server.py
echo if __name__ == '__main__': >> smtp_server.py
echo     handler = CustomHandler() >> smtp_server.py
echo     controller = Controller(handler, hostname='127.0.0.1', port=25) >> smtp_server.py
echo     controller.start() >> smtp_server.py
echo     print('Local SMTP server running on port 25. Press Ctrl+C to stop.') >> smtp_server.py
echo     try: >> smtp_server.py
echo         while True: >> smtp_server.py
echo             asyncio.get_event_loop().run_until_complete(asyncio.sleep(1)) >> smtp_server.py
echo     except KeyboardInterrupt: >> smtp_server.py
echo         pass >> smtp_server.py
echo     finally: >> smtp_server.py
echo         controller.stop() >> smtp_server.py

echo SMTP server script created.
echo To start the server, run: python smtp_server.py
echo.
echo Note: You may need to run as administrator to use port 25.
echo Alternatively, you can modify the port in the script and in php.ini

:: Create a shortcut to run as admin
echo Set oWS = WScript.CreateObject("WScript.Shell") > start_smtp_admin.vbs
echo sLinkFile = "Run SMTP Server (Admin).lnk" >> start_smtp_admin.vbs
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> start_smtp_admin.vbs
echo oLink.TargetPath = "cmd.exe" >> start_smtp_admin.vbs
echo oLink.Arguments = "/c python smtp_server.py" >> start_smtp_admin.vbs
echo oLink.Description = "Run SMTP Server as Admin" >> start_smtp_admin.vbs
echo oLink.HotKey = "" >> start_smtp_admin.vbs
echo oLink.IconLocation = "cmd.exe,0" >> start_smtp_admin.vbs
echo oLink.WindowStyle = 1 >> start_smtp_admin.vbs
echo oLink.WorkingDirectory = "%cd%" >> start_smtp_admin.vbs
echo oLink.Save >> start_smtp_admin.vbs
echo WScript.Echo "Shortcut created. Right-click on 'Run SMTP Server (Admin).lnk' and choose 'Run as administrator'" >> start_smtp_admin.vbs

cscript //nologo start_smtp_admin.vbs
del start_smtp_admin.vbs

echo Setup completed!
pause 