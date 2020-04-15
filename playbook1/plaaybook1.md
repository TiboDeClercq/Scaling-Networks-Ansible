# Playbook 1

In deze playbook gaan we kijken naar enkele eenvoudige en praktische toepassingen voor het beheren van een machine. Hierbij gaan we een aantal Docker containers gebruiken.

### Voorbereiding

Instaleer Ansible op je eigen computer. Je kan controleren of Ansible geïnstaleerd is met `ansible --version`.

1. Gebruik `docker pull` om de volgende containers binnen te halen:

- Centos
- Ubuntu
- Debian

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

### Schrijven van de eerste playbook

