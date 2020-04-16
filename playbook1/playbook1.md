# Eerste Playbook

### Voorbereiding

Instaleer Ansible op je eigen computer. Je kan controleren of Ansible geïnstaleerd is met `ansible --version`.

1. Gebruik `docker pull` om de volgende containers binnen te halen:

- Ubuntu

2. We draaien 2 ubuntu machines in de achtergrond met het commando:

```bash
docker run -dt --name ubuntu1 ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server net-tools vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

```bash
docker run -dt --name ubuntu2 ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server net-tools vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

3. shell functie om snel en makkelijk in de container te geraken:

```bash
u1() { docker exec -it ubuntu1 bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash"; }
```

```bash
u2() { docker exec -it ubuntu2 bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash"; }
```

4. Maak in beide containers een .ssh directory 

5. Kopieer de pubkey van de machine die Ansible draait in het authorized_keys bestand

6. Nu maak je een inventory aan voor de ansible playbook (zie inventory). Dit doe je door een bestand aan te maken met daarin het volgend: 

```
[Ubuntu]
172.17.0.2 ansible_ssh_user=root
172.17.0.3 ansible_ssh_user=root
```

* [Ubuntu] = De naam van de groep (je kiest dit zelf).
* 172.17.0.x = Het ip adres van de docker containers. 
* ansible_ssh_user = hiermee specificieren we welke user er wordt gebruikt voor de ssh verbinding

7. We kunnen nu testen of de machines toegankelijk zijn met het volgende commando:

```bash
ansible Ubuntu -i inventory -m command -a date
```

* Ubuntu is de groep
* -i specifieer de inventory 
* -m specifieer welke module je gaat gebruiken

Dit is het resultaat:

```
172.17.0.3 | CHANGED | rc=0 >>
Wed Apr 15 19:20:59 UTC 2020
172.17.0.2 | CHANGED | rc=0 >>
Wed Apr 15 19:20:59 UTC 2020
```

#### De eerste playbook

Een playbook is eigenlijk een 

We maken een nieuw .yml bestand aan voor onze playbook (zie playbook.yml). In deze playbook gaan we gebruik maken van de shell module om een bestand weg te schrijven op onze beide hosts.

```yml

#Eerste playbook 
#Wegschrijven van een bestand in /tmp/

#We beschrijven wat de playbook doot met name
- name: bestand wegschrijven in /tmp/ directory
  hosts: "*"    #Alle hosts in de inventory moeten dit uitvoeren
  tasks:        
    - name: Commando met shell module
      shell: echo "Onze eerste playbook werkt!" > /tmp/playbook1
      #Shell module gaat de tekst in  het bestand wegschrijven.
      #Dit is hetzelfde als je in bash of ander shells.
```


Nu kan je de playbook uitvoeren met het volgende commando:

```bash
ansible-playbook -i inventory playbook.yml
```

Dan krijg je de volgende output:

```bash
PLAY [bestand wegschrijven in /tmp/ directory] ********************************

TASK [Gathering Facts] ********************************************************
ok: [172.17.0.3]
ok: [172.17.0.2]

TASK [Commando met shell module] **********************************************
changed: [172.17.0.3]
changed: [172.17.0.2]

PLAY RECAP ********************************************************************
172.17.0.2                 : ok=2    changed=1    unreachable=0    failed=0
172.17.0.3                 : ok=2    changed=1    unreachable=0    failed=0
```

Als we nu in onze containers kijken, zien we dat we er een nieuw bestand is aangemaakt met de tekst "Onze eerste playbook werkt!".

```bash
ubuntu# cat /tmp/playbook1
Onze eerste playbook werkt!
ubuntu#
```

In de volgende playbook gaan verder in op het schrijven van een playbook en gebruiken meerder praktische modules.