# Análise de Segurança - Projeto SSI/IP
**InovaSoft Desenvolvimento de Sistemas**

## 1. Introdução

Este documento apresenta a análise de segurança da infraestrutura implementada, avaliando os três pilares fundamentais (CID), identificando vulnerabilidades e propondo melhorias.

---

## 2. Checklist CID (Confidencialidade, Integridade, Disponibilidade)

### 2.1 Confidencialidade ✅

| Item | Status | Evidência | Observações |
|------|--------|-----------|-------------|
| Controle de acesso por grupos implementado | ✅ Implementado | ACLs em `C:\Compartilhados\` | 5 grupos criados (TI, RH, Financeiro, Comercial, Público) |
| Usuários autenticam com credenciais únicas | ✅ Implementado | 4 usuários locais criados | Senhas atendem complexidade mínima |
| Pasta RH acessível apenas para GRP-RH | ✅ Validado | Teste negativo: usuário Comercial bloqueado | Log de acesso negado registrado |
| Pasta Financeiro isolada | ✅ Validado | Apenas GRP-Financeiro tem acesso | TI possui leitura para suporte |
| Aplicação web valida sessões | ✅ Implementado | `session_regenerate_id()` após login | Middleware `require_login()` |
| Senhas armazenadas com hash seguro | ✅ Implementado | `password_hash()` com bcrypt | Senhas não armazenadas em texto claro |
| Criptografia em trânsito (HTTPS) | ⚠️ Parcial | HTTP usado no laboratório | **Recomendação:** HTTPS com Let's Encrypt em produção |

**Pontos Fortes:**
- Segregação efetiva por departamento via ACLs NTFS + SMB
- Princípio do menor privilégio aplicado (usuários não têm privilégios admin por padrão)

**Pontos Fracos:**
- Tráfego de rede não criptografado (sem VPN site-to-site)
- Senhas de teste são fracas (Senha@123) — apenas aceitável em laboratório

**Score Confidencialidade:** 8/10

---

### 2.2 Integridade ✅

| Item | Status | Evidência | Observações |
|------|--------|-----------|-------------|
| Permissões de modificação controladas | ✅ Implementado | Apenas grupos autorizados podem modificar pastas | GRP-TI: Full Control; outros: Modify apenas em suas pastas |
| Backup regular de dados | ✅ Implementado | Script `backup-simulado.ps1` | Agendado para rodar diariamente |
| Versionamento de arquivos críticos | ⚠️ Não Implementado | Sem histórico de versões | **Recomendação:** Shadow Copy ou DFS com replicação |
| Auditoria de alterações em pastas | ⚠️ Parcial | Auditoria configurável via GPO | Não implementado no escopo acadêmico |
| Validação de entrada na aplicação web | ✅ Implementado | Prepared statements (PDO) | Previne SQL Injection |
| Sanitização de saída (XSS) | ✅ Implementado | `htmlspecialchars()` em outputs | Previne Cross-Site Scripting |
| Integridade de rotas (roteamento) | ✅ Validado | Rotas estáticas documentadas | Sem roteamento inesperado detectado |

**Pontos Fortes:**
- Prepared statements evitam SQL Injection
- Backups permitem recuperação de dados em caso de corrupção

**Pontos Fracos:**
- Sem detecção de alterações não autorizadas (ex: HIDS como Tripwire)
- Sem checksum ou assinatura digital em arquivos críticos

**Score Integridade:** 7/10

---

### 2.3 Disponibilidade ✅

| Item | Status | Evidência | Observações |
|------|--------|-----------|-------------|
| DHCP com failover (matriz + filiais) | ⚠️ Parcial | DHCP central com relay | Servidores de filial podem assumir manualmente |
| DNS com cache secundário | ✅ Implementado | SRV-FILIAL-A e SRV-FILIAL-B com forwarders | Se matriz cair, filiais mantêm cache temporariamente |
| Backup testado mensalmente | ✅ Planejado | Procedimento documentado | Teste de restauração obrigatório |
| Documentação de recuperação | ✅ Implementado | Cenários em `05-politicas-seguranca.md` | Inclui RTO/RPO |
| Redundância de links WAN | ❌ Não Implementado | Apenas 1 link por filial | **Limitação:** Escopo acadêmico |
| Monitoramento de serviços | ❌ Não Implementado | Sem alertas automáticos | **Recomendação:** Nagios, Zabbix ou PRTG |
| Plano de contingência documentado | ✅ Implementado | Procedimentos de retomada definidos | Ex: DHCP cair, ativar secundário |

**Pontos Fortes:**
- Documentação clara de procedimentos de recuperação
- Backup automatizado reduz risco de perda de dados

**Pontos Fracos:**
- Sem redundância física de servidores (ponto único de falha)
- Sem monitoramento proativo (detecção de problemas é reativa)

**Score Disponibilidade:** 6/10

---

## 3. Análise por Camada

### 3.1 Camada de Rede

| Aspecto | Implementação | Risco | Mitigação |
|---------|---------------|-------|-----------|
| Roteamento | Estático (3 rotas) | Baixo | Rotas documentadas, fácil debug |
| Segmentação | 3 redes /24 isoladas | Médio | Sem VLANs por departamento | **Melhoria:** VLANs + ACLs no switch |
| Firewall entre redes | ❌ Não implementado | Alto | Roteador permite tráfego irrestrito entre redes | **Recomendação:** ACLs no roteador ou firewall dedicado |
| Proteção contra ARP spoofing | ❌ Não implementado | Médio | Ataques MitM possíveis na LAN | **Melhoria:** Dynamic ARP Inspection |

### 3.2 Camada de Serviços

| Serviço | Exposição | Autenticação | Criptografia | Risco |
|---------|-----------|--------------|--------------|-------|
| DHCP | LAN (UDP 67/68) | ❌ Nenhuma | ❌ Não | Alto (DHCP spoofing possível) |
| DNS | LAN (UDP/TCP 53) | ❌ Nenhuma | ❌ Não | Médio (DNS poisoning possível) |
| SMB (Arquivos) | LAN (TCP 445) | ✅ Usuário/senha | ⚠️ SMB3 com criptografia | Baixo (com configuração correta) |
| HTTP (ProjectHub) | LAN (TCP 80) | ✅ Sessão PHP | ❌ Não | Médio (**usar HTTPS**) |
| SSH/RDP (Acesso admin) | LAN | ✅ Senha forte | ✅ RDP: TLS 1.2+ | Baixo |

**Recomendações Prioritárias:**
1. **HTTPS no ProjectHub:** Certificado SSL/TLS (Let's Encrypt ou autoassinado para testes)
2. **DNSSEC:** Prevenir envenenamento de cache DNS
3. **DHCP Snooping:** Proteger contra servidores DHCP falsos

### 3.3 Camada de Aplicação (ProjectHub)

| Vulnerabilidade | Status | Medida Implementada |
|-----------------|--------|---------------------|
| SQL Injection | ✅ Mitigado | PDO com prepared statements |
| XSS (Cross-Site Scripting) | ✅ Mitigado | `htmlspecialchars()` em outputs |
| CSRF (Cross-Site Request Forgery) | ✅ Mitigado | Token CSRF em formulários |
| Session Hijacking | ⚠️ Parcial | `session_regenerate_id()` após login, sem HTTPS |
| Exposição de Erros | ✅ Mitigado | `display_errors=0` em produção |
| Autenticação fraca | ⚠️ Risco Médio | Senha padrão em testes (**mudar em produção**) |
| Ausência de rate limiting | ❌ Vulnerável | Brute force possível no login | **Melhoria:** Limitar tentativas por IP |

**OWASP Top 10 (2021) - Compliance:**

| # | Vulnerabilidade | Status InovaSoft |
|---|-----------------|------------------|
| A01 | Broken Access Control | ✅ Mitigado (guards por grupo) |
| A02 | Cryptographic Failures | ⚠️ Parcial (HTTP em vez de HTTPS) |
| A03 | Injection | ✅ Mitigado (prepared statements) |
| A04 | Insecure Design | ✅ Adequado (arquitetura revisada) |
| A05 | Security Misconfiguration | ⚠️ Parcial (alguns defaults inseguros) |
| A06 | Vulnerable Components | ✅ OK (PHP 8.x atualizado) |
| A07 | Identification/Auth Failures | ⚠️ Risco (senhas fracas, sem MFA) |
| A08 | Software and Data Integrity | ⚠️ Parcial (sem verificação de integridade de updates) |
| A09 | Security Logging/Monitoring | ❌ Ausente (sem logs centralizados) |
| A10 | Server-Side Request Forgery | ✅ Não aplicável (sem funcionalidade de fetch externa) |

---

## 4. Cenários de Ataque e Simulações

### Cenário 1: Tentativa de Acesso Não Autorizado (Teste Negativo)

**Descrição:** Usuário do grupo Comercial tenta acessar pasta Financeiro.

**Execução:**
1. Fazer login como `erik.doca` (Comercial) em PC-MATRIZ-01
2. Tentar abrir `\\192.168.10.10\Financeiro`

**Resultado Esperado:** ❌ Acesso negado  
**Resultado Obtido:** ✅ "Você não tem permissão para acessar esta pasta"  
**Conclusão:** Controle de acesso FUNCIONANDO corretamente.

**Log (Event Viewer no servidor):**
```
Event ID 5145 - Security Auditing
Failure: Object Access
Account: erik.doca
Object: \\SRV-MATRIZ\Financeiro
Access Requested: ReadData
```

---

### Cenário 2: Simulação de Falha do Servidor DHCP

**Descrição:** Servidor SRV-MATRIZ (DHCP central) fica offline.

**Sintomas Observados:**
- PCs tentam renovar IP e falham
- IPs mudam para faixa 169.254.x.x (APIPA)
- Conectividade de rede perdida

**Diagnóstico:**
```cmd
ipconfig /all
# Mostra: Autoconfiguration IPv4 Address: 169.254.x.x
# DHCP Enabled: Yes
# DHCP Server: (não alcançável)
```

**Workaround Aplicado:**
1. Ativar DHCP no SRV-FILIAL-A manualmente:
   ```powershell
   Install-WindowsFeature DHCP
   Add-DhcpServerv4Scope -Name "Escopo-Emergencia" -StartRange 192.168.20.100 -EndRange 192.168.20.150 -SubnetMask 255.255.255.0
   Set-DhcpServerv4OptionValue -Router 192.168.20.1 -DnsServer 192.168.10.10
   ```
2. PCs renovam IP: `ipconfig /renew`

**Tempo de Recuperação:** 25 minutos (dentro do RTO de 30 min)  
**Conclusão:** Plano de contingência FUNCIONAL.

---

### Cenário 3: Ataque de SQL Injection (Teste de Penetração)

**Descrição:** Tentar injetar SQL malicioso no formulário de login do ProjectHub.

**Tentativa 1:**
- Username: `admin' OR '1'='1`
- Password: `qualquercoisa`

**Resultado Obtido:** ❌ Login falhou, consulta não executou SQL malicioso.  
**Motivo:** PDO com prepared statements escapa automaticamente.

**Código que preveniu o ataque:**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password_hash = ?");
$stmt->execute([$username, $password_hash]);
// O PDO trata '?' como parâmetro, não como SQL literal
```

**Conclusão:** SQL Injection MITIGADO com sucesso.

---

### Cenário 4: Teste de Backup e Restauração

**Descrição:** Simular perda de dados e restaurar do backup.

**Execução:**
1. Criar arquivo de teste em `C:\Compartilhados\RH\teste-critico.docx`
2. Executar backup: `.\backup-simulado.ps1 -Full`
3. Deletar arquivo intencionalmente
4. Restaurar do backup:
   ```powershell
   $ultimoBackup = Get-ChildItem C:\Backups\Backup_*.zip | Sort-Object CreationTime -Descending | Select-Object -First 1
   Expand-Archive -Path $ultimoBackup.FullName -DestinationPath C:\Restore-Teste
   Copy-Item C:\Restore-Teste\RH\teste-critico.docx -Destination C:\Compartilhados\RH\
   ```

**Resultado:** ✅ Arquivo restaurado com sucesso, conteúdo íntegro.  
**Tempo de Restauração:** 8 minutos (dentro do RTO de 2h)  
**RPO:** 0 horas (backup foi feito imediatamente antes)

**Conclusão:** Processo de backup/restore VALIDADO.

---

## 5. Matriz de Riscos

| Ameaça | Probabilidade | Impacto | Risco | Mitigação Atual | Melhoria Recomendada |
|--------|---------------|---------|-------|-----------------|----------------------|
| Perda de dados (falha de HD) | Média | Alto | 🟠 Médio | Backup diário | RAID 1 no servidor + backup offsite |
| Ataque de ransomware | Baixa | Crítico | 🟠 Médio | Senhas fortes, ACLs | Antivírus, EDR, treinamento |
| Interrupção de link WAN | Alta | Médio | 🟠 Médio | Trabalho local temporário | Link redundante (4G/5G backup) |
| Vazamento de credenciais | Média | Alto | 🟠 Médio | Senhas com hash bcrypt | MFA (autenticação de dois fatores) |
| Acesso físico não autorizado | Baixa | Alto | 🟢 Baixo | Sala de servidores com acesso restrito | Biometria, câmeras |
| DDoS na aplicação web | Baixa | Médio | 🟢 Baixo | App interna (não exposta à internet) | WAF se publicar externamente |
| Erro humano (delete acidental) | Alta | Médio | 🟠 Médio | Backup + Shadow Copy | Treinamento de usuários |

**Legenda:**  
🟢 Baixo | 🟠 Médio | 🔴 Alto

---

## 6. Pontos Fortes da Implementação

1. **Segregação de Acesso:** ACLs bem definidas por departamento impedem vazamento lateral de informações.
2. **Autenticação Obrigatória:** Tanto na rede (usuários locais) quanto na aplicação (login no ProjectHub).
3. **Preparação para Recuperação:** Documentação clara de procedimentos de contingência e backup automatizado.
4. **Segurança Básica da Aplicação:** Prepared statements, CSRF tokens e sanitização de outputs.
5. **Princípio do Menor Privilégio:** Usuários têm apenas permissões necessárias, admin restrito a TI.

---

## 7. Pontos Fracos e Vulnerabilidades Identificadas

| # | Vulnerabilidade | Severidade | Impacto | Recomendação |
|---|-----------------|------------|---------|--------------|
| 1 | Falta de HTTPS na aplicação web | 🟠 Média | Credenciais podem ser interceptadas em texto claro | Implementar TLS/SSL |
| 2 | Senhas fracas em ambiente de teste | 🟠 Média | Facilita brute force | Gerar senhas fortes e únicas |
| 3 | Sem redundância de hardware | 🔴 Alta | Servidor matriz é ponto único de falha | Cluster ou VM com failover |
| 4 | Ausência de IDS/IPS | 🟠 Média | Ataques não detectados em tempo real | Snort, Suricata ou similar |
| 5 | Sem autenticação multifator (MFA) | 🟠 Média | Credenciais comprometidas garantem acesso | Google Authenticator, Duo |
| 6 | Logs não centralizados | 🟢 Baixa | Dificulta análise forense | Syslog server ou SIEM |
| 7 | Falta de firewall entre redes | 🔴 Alta | Tráfego entre filiais não filtrado | ACLs no roteador ou firewall Fortinet/pfSense |
| 8 | Sem criptografia de dados em repouso | 🟢 Baixa | Dados podem ser lidos se HD for roubado | BitLocker ou dm-crypt |

---

## 8. Plano de Melhorias Futuras

### Curto Prazo (1-3 meses)
- [ ] Implementar HTTPS no ProjectHub com Let's Encrypt
- [ ] Substituir senhas de teste por senhas individuais fortes
- [ ] Habilitar auditoria de acesso em pastas confidenciais (GPO)
- [ ] Configurar ACLs básicas no roteador (bloquear portas desnecessárias)

### Médio Prazo (3-6 meses)
- [ ] Implementar MFA para usuários admin
- [ ] Configurar IDS (Snort) em modo passivo para monitoramento
- [ ] Migrar para Active Directory (facilita gestão centralizada)
- [ ] Implementar Shadow Copy para versionamento de arquivos

### Longo Prazo (6-12 meses)
- [ ] Implantar servidor de backup secundário (offsite ou nuvem)
- [ ] Contratar link WAN redundante (MPLS ou SD-WAN)
- [ ] SIEM para análise de logs (Splunk, Graylog ou ELK Stack)
- [ ] Treinamento anual de conscientização em segurança para todos os usuários

---

## 9. Conclusão da Análise

A infraestrutura implementada atende aos requisitos básicos de segurança para um ambiente acadêmico/PME, com **forte aderência aos pilares de Confidencialidade e Integridade**, mas com **oportunidades de melhoria em Disponibilidade**.

### Resumo de Scores:
- **Confidencialidade:** 8/10 ✅
- **Integridade:** 7/10 ✅
- **Disponibilidade:** 6/10 ⚠️

**Nota Geral:** 7/10 (Bom, com pontos de atenção)

### Principais Conquistas:
- Controle de acesso granular funcional e testado
- Aplicação web protegida contra vulnerabilidades comuns (SQL Injection, XSS, CSRF)
- Backup automatizado e testado

### Principais Desafios:
- Falta de redundância física
- Ausência de monitoramento proativo
- Criptografia em trânsito apenas parcial

**Aprendizado:** A segurança é um processo contínuo, não um estado final. As melhorias propostas elevam a maturidade de segurança conforme o orçamento e complexidade da operação crescem.

---

**Versão:** 1.0  
**Data:** 20/10/2025  
**Responsáveis:** Eric Santos, Emilly Gonçalves, João Pedro Vianna  
**Revisado por:** Equipe InovaSoft SSI/IP

