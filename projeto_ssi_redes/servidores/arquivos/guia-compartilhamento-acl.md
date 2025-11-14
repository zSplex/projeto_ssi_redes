# Guia de Compartilhamento de Arquivos e ACLs
**Projeto SSI/IP - InovaSoft**

Este documento detalha a criação de pastas compartilhadas com controle de acesso por grupos (ACLs) no Windows Server.

---

## 1. Visão Geral

### 1.1 Objetivo

Criar estrutura de pastas com permissões diferenciadas por departamento, garantindo:
- **Confidencialidade:** Cada grupo acessa apenas suas pastas
- **Integridade:** Controle de quem pode modificar/apagar arquivos
- **Disponibilidade:** Compartilhamento acessível de qualquer filial

### 1.2 Grupos e Pastas

| Grupo Local | Pasta Compartilhada | Permissões |
|-------------|---------------------|------------|
| TI | `\\SRV-MATRIZ\TI` | Controle Total |
| RH | `\\SRV-MATRIZ\RH` | Modificar (RH), Negar (outros) |
| Financeiro | `\\SRV-MATRIZ\Financeiro` | Modificar (Financeiro), Negar (outros) |
| Comercial | `\\SRV-MATRIZ\Comercial` | Modificar (Comercial), Leitura (TI) |
| Publico | `\\SRV-MATRIZ\Publico` | Leitura (Todos), Modificar (TI) |

---

## 2. Criar Grupos Locais

### 2.1 Via Interface Gráfica

1. Abrir **Computer Management** (compmgmt.msc)
2. Expandir **Local Users and Groups** → **Groups**
3. Botão direito em **Groups** → **New Group**

**Criar os seguintes grupos:**

| Nome do Grupo | Descrição |
|---------------|-----------|
| GRP-TI | Equipe de Tecnologia da Informação |
| GRP-RH | Departamento de Recursos Humanos |
| GRP-Financeiro | Departamento Financeiro |
| GRP-Comercial | Equipe Comercial e Vendas |
| GRP-Publico | Acesso a documentos públicos |

4. Para cada grupo:
   - **Group name:** Ex: `GRP-TI`
   - **Description:** Preencher conforme tabela
   - **Create** → **Close**

### 2.2 Via PowerShell

```powershell
# Script: Criar-Grupos.ps1
New-LocalGroup -Name "GRP-TI" -Description "Equipe de TI - Acesso total"
New-LocalGroup -Name "GRP-RH" -Description "Recursos Humanos - Dados sensíveis"
New-LocalGroup -Name "GRP-Financeiro" -Description "Financeiro - Dados confidenciais"
New-LocalGroup -Name "GRP-Comercial" -Description "Comercial e Vendas"
New-LocalGroup -Name "GRP-Publico" -Description "Todos os usuários - Leitura geral"

Write-Host "Grupos locais criados com sucesso!" -ForegroundColor Green
```

---

## 3. Criar Usuários Locais

### 3.1 Via Interface Gráfica

1. **Computer Management** → **Local Users and Groups** → **Users**
2. Botão direito em **Users** → **New User**

**Usuário 1: Eric Santos (TI - Admin)**
- **User name:** `eric.santos`
- **Full name:** `Eric Santos`
- **Description:** `Administrador de TI`
- **Password:** `Senha@123` (temporária)
- Desmarcar: ☐ User must change password at next logon
- Marcar: ☑ User cannot change password (apenas para ambiente de testes)
- Marcar: ☑ Password never expires (apenas para testes)
- **Create**

Repetir para:

| Nome Completo | Username | Grupo | Descrição |
|---------------|----------|-------|-----------|
| Eric Santos | eric.santos | GRP-TI | Administrador de TI |
| Erik Doca | erik.doca | GRP-Comercial | Analista Comercial |
| Emilly Gonçalves | emilly.goncalves | GRP-RH | Analista de RH |
| João Pedro Vianna | joao.vianna | GRP-Financeiro | Analista Financeiro |

### 3.2 Adicionar Usuários aos Grupos

1. **Computer Management** → **Groups** → Duplo clique em **GRP-TI**
2. **Add** → Digitar `eric.santos` → **Check Names** → OK
3. Repetir para cada usuário/grupo conforme tabela acima

### 3.3 Via PowerShell (Recomendado)

```powershell
# Script: Criar-Usuarios.ps1
# Executar como Administrador

# Senha padrão (em produção, usar senhas individuais)
$senha = ConvertTo-SecureString "Senha@123" -AsPlainText -Force

# Criar usuário Eric (TI)
New-LocalUser -Name "eric.santos" -FullName "Eric Santos" -Description "Administrador de TI" -Password $senha -PasswordNeverExpires
Add-LocalGroupMember -Group "GRP-TI" -Member "eric.santos"
Add-LocalGroupMember -Group "Administrators" -Member "eric.santos"  # Admin local

# Criar usuário Erik (Comercial)
New-LocalUser -Name "erik.doca" -FullName "Erik Doca" -Description "Analista Comercial" -Password $senha -PasswordNeverExpires
Add-LocalGroupMember -Group "GRP-Comercial" -Member "erik.doca"
Add-LocalGroupMember -Group "GRP-Publico" -Member "erik.doca"

# Criar usuária Emilly (RH)
New-LocalUser -Name "emilly.goncalves" -FullName "Emilly Gonçalves" -Description "Analista de RH" -Password $senha -PasswordNeverExpires
Add-LocalGroupMember -Group "GRP-RH" -Member "emilly.goncalves"
Add-LocalGroupMember -Group "GRP-Publico" -Member "emilly.goncalves"

# Criar usuário João (Financeiro)
New-LocalUser -Name "joao.vianna" -FullName "João Pedro Vianna" -Description "Analista Financeiro" -Password $senha -PasswordNeverExpires
Add-LocalGroupMember -Group "GRP-Financeiro" -Member "joao.vianna"
Add-LocalGroupMember -Group "GRP-Publico" -Member "joao.vianna"

Write-Host "Usuários criados e adicionados aos grupos!" -ForegroundColor Green

# Verificar
Get-LocalGroupMember -Group "GRP-TI"
Get-LocalGroupMember -Group "GRP-RH"
Get-LocalGroupMember -Group "GRP-Financeiro"
Get-LocalGroupMember -Group "GRP-Comercial"
```

**Observação de Segurança:** A senha `Senha@123` é apenas para ambiente de testes. Em produção, usar senhas individuais fortes e exigir troca no primeiro logon.

---

## 4. Criar Estrutura de Pastas

### 4.1 Criar Diretório Base

1. Abrir **File Explorer** → Navegar para `C:\`
2. Criar pasta: `C:\Compartilhados\`
3. Dentro dela, criar subpastas:

```
C:\Compartilhados\
├── TI\
├── RH\
├── Financeiro\
├── Comercial\
└── Publico\
```

### 4.2 Via PowerShell

```powershell
# Criar estrutura de pastas
$basePath = "C:\Compartilhados"
New-Item -Path $basePath -ItemType Directory -Force
New-Item -Path "$basePath\TI" -ItemType Directory -Force
New-Item -Path "$basePath\RH" -ItemType Directory -Force
New-Item -Path "$basePath\Financeiro" -ItemType Directory -Force
New-Item -Path "$basePath\Comercial" -ItemType Directory -Force
New-Item -Path "$basePath\Publico" -ItemType Directory -Force

Write-Host "Estrutura de pastas criada em $basePath" -ForegroundColor Green
```

---

## 5. Configurar Permissões NTFS

### 5.1 Princípio: Desabilitar Herança e Definir ACLs Explícitas

Para cada pasta, vamos:
1. Remover herança de permissões
2. Remover grupo "Users" (se existir)
3. Adicionar permissões específicas por grupo

### 5.2 Pasta TI (Controle Total para GRP-TI)

**Via GUI:**
1. Clicar com botão direito em `C:\Compartilhados\TI` → **Properties**
2. Aba **Security** → **Advanced**
3. **Disable inheritance** → **Remove all inherited permissions from this object**
4. **Add** → **Select a principal** → Digitar `GRP-TI` → **Check Names** → OK
5. **Basic permissions:** Marcar **Full Control** → OK
6. **Add** novamente → Principal: `Administrators` → Full Control → OK
7. **Add** → Principal: `SYSTEM` → Full Control → OK
8. Apply → OK

**Via PowerShell:**
```powershell
$path = "C:\Compartilhados\TI"

# Desabilitar herança e remover permissões herdadas
$acl = Get-Acl $path
$acl.SetAccessRuleProtection($true, $false)
Set-Acl $path $acl

# Adicionar permissões
$acl = Get-Acl $path

# GRP-TI: Controle Total
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-TI", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# Administrators: Controle Total
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# SYSTEM: Controle Total
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("SYSTEM", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

Set-Acl $path $acl
Write-Host "Permissões configuradas para pasta TI" -ForegroundColor Green
```

### 5.3 Pasta RH (Acesso Apenas para GRP-RH)

```powershell
$path = "C:\Compartilhados\RH"
$acl = Get-Acl $path
$acl.SetAccessRuleProtection($true, $false)
Set-Acl $path $acl

$acl = Get-Acl $path

# GRP-RH: Modificar
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-RH", "Modify", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# Administrators e SYSTEM
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("SYSTEM", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

Set-Acl $path $acl
```

### 5.4 Pasta Financeiro (Acesso Apenas para GRP-Financeiro)

```powershell
$path = "C:\Compartilhados\Financeiro"
$acl = Get-Acl $path
$acl.SetAccessRuleProtection($true, $false)
Set-Acl $path $acl

$acl = Get-Acl $path
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-Financeiro", "Modify", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("SYSTEM", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
Set-Acl $path $acl
```

### 5.5 Pasta Comercial (Acesso GRP-Comercial + Leitura GRP-TI)

```powershell
$path = "C:\Compartilhados\Comercial"
$acl = Get-Acl $path
$acl.SetAccessRuleProtection($true, $false)
Set-Acl $path $acl

$acl = Get-Acl $path

# GRP-Comercial: Modificar
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-Comercial", "Modify", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# GRP-TI: Leitura (para suporte)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-TI", "ReadAndExecute", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# Administrators e SYSTEM
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("SYSTEM", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

Set-Acl $path $acl
```

### 5.6 Pasta Publico (Leitura para Todos, Modificar apenas TI)

```powershell
$path = "C:\Compartilhados\Publico"
$acl = Get-Acl $path
$acl.SetAccessRuleProtection($true, $false)
Set-Acl $path $acl

$acl = Get-Acl $path

# GRP-Publico: Leitura
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-Publico", "ReadAndExecute", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# GRP-TI: Modificar
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("GRP-TI", "Modify", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

# Administrators e SYSTEM
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)
$ar = New-Object System.Security.AccessControl.FileSystemAccessRule("SYSTEM", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.AddAccessRule($ar)

Set-Acl $path $acl
```

---

## 6. Compartilhar Pastas na Rede

### 6.1 Via Interface Gráfica

1. Botão direito na pasta → **Properties** → Aba **Sharing**
2. **Advanced Sharing** → Marcar **Share this folder**
3. **Share name:** Nome do compartilhamento (ex: `TI`)
4. **Permissions** → **Add** → `GRP-TI` → Marcar **Full Control**
5. Remover grupo **Everyone** (se existir)
6. Apply → OK

### 6.2 Via PowerShell (Todas as Pastas)

```powershell
# Compartilhar pasta TI
New-SmbShare -Name "TI" -Path "C:\Compartilhados\TI" -FullAccess "GRP-TI","Administrators"

# Compartilhar pasta RH
New-SmbShare -Name "RH" -Path "C:\Compartilhados\RH" -FullAccess "GRP-RH","Administrators"

# Compartilhar pasta Financeiro
New-SmbShare -Name "Financeiro" -Path "C:\Compartilhados\Financeiro" -FullAccess "GRP-Financeiro","Administrators"

# Compartilhar pasta Comercial
New-SmbShare -Name "Comercial" -Path "C:\Compartilhados\Comercial" -FullAccess "GRP-Comercial","Administrators" -ReadAccess "GRP-TI"

# Compartilhar pasta Publico
New-SmbShare -Name "Publico" -Path "C:\Compartilhados\Publico" -ChangeAccess "GRP-TI" -ReadAccess "GRP-Publico"

Write-Host "Pastas compartilhadas criadas com sucesso!" -ForegroundColor Green
Get-SmbShare | Where-Object {$_.Path -like "C:\Compartilhados\*"}
```

---

## 7. Matriz de Permissões (Referência Rápida)

| Pasta | GRP-TI | GRP-RH | GRP-Financeiro | GRP-Comercial | GRP-Publico |
|-------|--------|--------|----------------|---------------|-------------|
| **TI** | Controle Total | ❌ Negado | ❌ Negado | ❌ Negado | ❌ Negado |
| **RH** | ✅ Leitura* | ✅ Modificar | ❌ Negado | ❌ Negado | ❌ Negado |
| **Financeiro** | ✅ Leitura* | ❌ Negado | ✅ Modificar | ❌ Negado | ❌ Negado |
| **Comercial** | ✅ Leitura | ❌ Negado | ❌ Negado | ✅ Modificar | ❌ Negado |
| **Publico** | ✅ Modificar | ✅ Leitura | ✅ Leitura | ✅ Leitura | ✅ Leitura |

\* **Leitura para TI:** Necessária para suporte técnico (pode ser ajustada conforme política)

---

## 8. Testar Permissões

### 8.1 Teste Positivo (Acesso Permitido)

1. Em um PC cliente, fazer logon como `emilly.goncalves` (senha: `Senha@123`)
2. Abrir **File Explorer** → Digitar na barra: `\\192.168.10.10\RH`
3. **Resultado esperado:** Pasta abre, permite criar/editar arquivos

### 8.2 Teste Negativo (Acesso Negado)

1. Ainda logado como `emilly.goncalves`
2. Tentar acessar: `\\192.168.10.10\Financeiro`
3. **Resultado esperado:** Erro "Acesso negado" ou "Você não tem permissão"

### 8.3 Teste de Leitura vs Modificação

1. Fazer logon como `erik.doca` (Comercial)
2. Acessar `\\192.168.10.10\Publico`
3. Tentar criar arquivo → **Resultado esperado:** Falha (apenas leitura)
4. Fazer logon como `eric.santos` (TI)
5. Acessar `\\192.168.10.10\Publico`
6. Criar arquivo "teste.txt" → **Resultado esperado:** Sucesso

### 8.4 Script de Teste Automatizado

```powershell
# Script: Testar-Permissoes.ps1
# Executar no servidor

$testFile = "C:\Compartilhados\RH\teste-rh.txt"

# Tentar criar arquivo como usuário RH
$cred = Get-Credential -UserName "emilly.goncalves" -Message "Digite a senha"
Start-Process powershell -Credential $cred -ArgumentList "-Command `"New-Item -Path '$testFile' -ItemType File -Force`""

Start-Sleep -Seconds 2
if (Test-Path $testFile) {
    Write-Host "✅ Teste RH: Usuário conseguiu criar arquivo (CORRETO)" -ForegroundColor Green
} else {
    Write-Host "❌ Teste RH: Falha ao criar arquivo" -ForegroundColor Red
}
```

---

## 9. Script Completo de Configuração

Salvar como `Configurar-Compartilhamento-Completo.ps1`:

```powershell
# ========================================
# Script Completo: Configuração de Compartilhamento e ACLs
# Projeto SSI/IP - InovaSoft
# ========================================

Write-Host "=== Iniciando Configuração ===" -ForegroundColor Cyan

# 1. Criar Grupos
Write-Host "`n[1/5] Criando grupos locais..." -ForegroundColor Yellow
New-LocalGroup -Name "GRP-TI" -Description "Equipe de TI" -ErrorAction SilentlyContinue
New-LocalGroup -Name "GRP-RH" -Description "Recursos Humanos" -ErrorAction SilentlyContinue
New-LocalGroup -Name "GRP-Financeiro" -Description "Financeiro" -ErrorAction SilentlyContinue
New-LocalGroup -Name "GRP-Comercial" -Description "Comercial" -ErrorAction SilentlyContinue
New-LocalGroup -Name "GRP-Publico" -Description "Acesso público" -ErrorAction SilentlyContinue

# 2. Criar Usuários
Write-Host "[2/5] Criando usuários..." -ForegroundColor Yellow
$senha = ConvertTo-SecureString "Senha@123" -AsPlainText -Force

$usuarios = @(
    @{Name="eric.santos"; FullName="Eric Santos"; Group="GRP-TI"; Admin=$true},
    @{Name="erik.doca"; FullName="Erik Doca"; Group="GRP-Comercial"; Admin=$false},
    @{Name="emilly.goncalves"; FullName="Emilly Gonçalves"; Group="GRP-RH"; Admin=$false},
    @{Name="joao.vianna"; FullName="João Pedro Vianna"; Group="GRP-Financeiro"; Admin=$false}
)

foreach ($u in $usuarios) {
    New-LocalUser -Name $u.Name -FullName $u.FullName -Password $senha -PasswordNeverExpires -ErrorAction SilentlyContinue
    Add-LocalGroupMember -Group $u.Group -Member $u.Name -ErrorAction SilentlyContinue
    Add-LocalGroupMember -Group "GRP-Publico" -Member $u.Name -ErrorAction SilentlyContinue
    if ($u.Admin) {
        Add-LocalGroupMember -Group "Administrators" -Member $u.Name -ErrorAction SilentlyContinue
    }
}

# 3. Criar Estrutura de Pastas
Write-Host "[3/5] Criando estrutura de pastas..." -ForegroundColor Yellow
$basePath = "C:\Compartilhados"
$pastas = @("TI", "RH", "Financeiro", "Comercial", "Publico")

New-Item -Path $basePath -ItemType Directory -Force | Out-Null
foreach ($pasta in $pastas) {
    New-Item -Path "$basePath\$pasta" -ItemType Directory -Force | Out-Null
}

# 4. Configurar Permissões NTFS
Write-Host "[4/5] Configurando permissões NTFS..." -ForegroundColor Yellow

function Set-FolderPermission {
    param($Path, $Groups)
    
    $acl = Get-Acl $Path
    $acl.SetAccessRuleProtection($true, $false)
    Set-Acl $Path $acl
    
    $acl = Get-Acl $Path
    
    foreach ($g in $Groups) {
        $ar = New-Object System.Security.AccessControl.FileSystemAccessRule($g.Name, $g.Rights, "ContainerInherit,ObjectInherit", "None", "Allow")
        $acl.AddAccessRule($ar)
    }
    
    Set-Acl $Path $acl
}

# Permissões por pasta
Set-FolderPermission -Path "$basePath\TI" -Groups @(
    @{Name="GRP-TI"; Rights="FullControl"},
    @{Name="Administrators"; Rights="FullControl"},
    @{Name="SYSTEM"; Rights="FullControl"}
)

Set-FolderPermission -Path "$basePath\RH" -Groups @(
    @{Name="GRP-RH"; Rights="Modify"},
    @{Name="Administrators"; Rights="FullControl"},
    @{Name="SYSTEM"; Rights="FullControl"}
)

Set-FolderPermission -Path "$basePath\Financeiro" -Groups @(
    @{Name="GRP-Financeiro"; Rights="Modify"},
    @{Name="Administrators"; Rights="FullControl"},
    @{Name="SYSTEM"; Rights="FullControl"}
)

Set-FolderPermission -Path "$basePath\Comercial" -Groups @(
    @{Name="GRP-Comercial"; Rights="Modify"},
    @{Name="GRP-TI"; Rights="ReadAndExecute"},
    @{Name="Administrators"; Rights="FullControl"},
    @{Name="SYSTEM"; Rights="FullControl"}
)

Set-FolderPermission -Path "$basePath\Publico" -Groups @(
    @{Name="GRP-Publico"; Rights="ReadAndExecute"},
    @{Name="GRP-TI"; Rights="Modify"},
    @{Name="Administrators"; Rights="FullControl"},
    @{Name="SYSTEM"; Rights="FullControl"}
)

# 5. Criar Compartilhamentos SMB
Write-Host "[5/5] Criando compartilhamentos de rede..." -ForegroundColor Yellow

New-SmbShare -Name "TI" -Path "$basePath\TI" -FullAccess "GRP-TI","Administrators" -ErrorAction SilentlyContinue
New-SmbShare -Name "RH" -Path "$basePath\RH" -FullAccess "GRP-RH","Administrators" -ErrorAction SilentlyContinue
New-SmbShare -Name "Financeiro" -Path "$basePath\Financeiro" -FullAccess "GRP-Financeiro","Administrators" -ErrorAction SilentlyContinue
New-SmbShare -Name "Comercial" -Path "$basePath\Comercial" -FullAccess "GRP-Comercial","Administrators" -ReadAccess "GRP-TI" -ErrorAction SilentlyContinue
New-SmbShare -Name "Publico" -Path "$basePath\Publico" -ChangeAccess "GRP-TI" -ReadAccess "GRP-Publico" -ErrorAction SilentlyContinue

Write-Host "`n=== Configuração Concluída ===" -ForegroundColor Green
Write-Host "Compartilhamentos criados:" -ForegroundColor Cyan
Get-SmbShare | Where-Object {$_.Path -like "$basePath\*"} | Format-Table Name, Path, Description
```

---

## 10. Checklist de Validação

- [ ] 5 grupos locais criados (GRP-TI, GRP-RH, GRP-Financeiro, GRP-Comercial, GRP-Publico)
- [ ] 4 usuários criados e adicionados aos grupos correspondentes
- [ ] 5 pastas criadas em `C:\Compartilhados\`
- [ ] Permissões NTFS configuradas (herança desabilitada)
- [ ] Compartilhamentos SMB criados (`\\SRV-MATRIZ\TI`, etc.)
- [ ] Teste positivo: usuário RH acessa pasta RH
- [ ] Teste negativo: usuário RH não acessa pasta Financeiro
- [ ] Teste de leitura: usuário Comercial lê pasta Publico mas não modifica

---

**Versão:** 1.0  
**Última atualização:** 06/10/2025  
**Responsáveis:** Eric Santos, Emilly Gonçalves

