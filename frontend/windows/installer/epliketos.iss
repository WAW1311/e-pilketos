[Setup]
AppName=Epilketos
AppVersion=1.0.0
DefaultDirName={autopf}\Epilketos
DefaultGroupName=Epilketos
OutputDir=output
OutputBaseFilename=Epilketos-Setup
Compression=lzma
SolidCompression=yes
WizardStyle=modern
ArchitecturesAllowed=x64compatible
ArchitecturesInstallIn64BitMode=x64compatible
SetupIconFile=..\runner\resources\app_icon.ico

[Files]
Source: "..\..\build\windows\x64\runner\Release\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs

[Icons]
Name: "{group}\Epilketos"; Filename: "{app}\Epilketos.exe"
Name: "{autodesktop}\Epilketos"; Filename: "{app}\Epilketos.exe"

[Run]
Filename: "{app}\Epilketos.exe"; Description: "Jalankan Epilketos"; Flags: postinstall skipifsilent