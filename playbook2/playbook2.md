# Playbook 2

In deze playbook gaan we gebruiken maken van een aantal praktische modules bij het beheren van verschillende soorten servers. We maken gebruiken van de volgende modules:

* File module
* Command module
* Shell module
* Copy module
* Fetch module
* apt module (voor debian, ubuntu, ...)
* yum module (voor centos, ...)

#### Voorbereiding

Haal de volgende containers binnen met ``` Docker pull```
 * Debian
 * Ubuntu

We draaien de containers in de achtergrond en  maken de volgende shell functies om makkelijk aan onze containers te geraken:

**Ubuntu**

```bash
docker run -dt --name ubuntu1 ubuntu:latest bash -c "apt-get update; apt-get install -y openssh-server vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

```bash
u1() { docker exec -it ubuntu1 bash -c "echo 'PS1='\''ubuntu# '\' >> /root/.bashrc; bash"; }
```

**Debian**

Bij Debian instanties moet je python3 extra instaleren!

```bash
docker run -dt --name debian1 debian:latest bash -c "apt-get update; apt-get install -y openssh-server python3 vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

```bash
d1() { docker exec -it debian1 bash -c "echo 'PS1='\''debian# '\' >> /root/.bashrc; bash"; }
```
Ik heb een Centos docker container binnengehaald met docker pull, daarna in de container openssh-server openssh-clients geinstaleerd. Mijn private key in de authorized_keys gestoken. Wanneer ik een ssh verbinding probeer te maken naar de Centos container lukt dit mij niet (connectie op port 22 wordt gewijgerd). Bij de Ubuntu en Debian container is dit mij wel gelukt op dezelfde manier. 

Ik heb in de config van sshd_config root login geacepteerd en poort
<!--
Problemen met ssh, geen idee waarom :(
 **Centos**

```bash
docker run -dt --name centos1 centos:latest bash -c "yum update -y; yum install -y openssh-server openssh-clients net-tools vim; service ssh start; while true; do sleep 60; echo keepalive; done"
```

```bash
 c1() { docker exec -it centos1 bash -c "echo 'PS1='\''centos# '\' >> /root/.bashrc; bash"; }
```
-->

Je kan gemakkelijke de ip adressen van de container opvragen met het volgende commando:

```bash
docker inspect -f "{{ .NetworkSettings.IPAddress }}" container_name
```
Inventory:

```bash
[Ubuntu]
172.17.0.2 ansible_ssh_user=root
[Debian]
172.17.0.3 ansible_ssh_user=root
```

#### File module

###### Directory maken

We gaan voor alle hosts van de inventory een nieuwe folder aanmaken: *user1* 

```yml
- name: bestanden voor user1 
  hosts: "*"
  tasks:
    - name: nieuwe directory maken
      file:
        path: $HOME/home/user1
        state: directory
```

Het uitvoeren van de playbook doe je met:

```bash
ansible-playbook file.yml -i inventory
```

We willen nu voor user1 een een aantal verschillende folders aanmaken. Dit doen we door gebruik te maken van een lus.

```yml
   - name: meerder folders voor user1
      file:
        path: $HOME/home/user1/{{item}}
        state: directory
      loop:
        - Downloads
        - Documenten
        - Muziek
        - Afbeelding
        - test123
        - test456
        - test789
        - scripts
```

```bash
debian# ls -alh /root/home/user1/
total 40K
drwxr-xr-x 10 root root 4.0K Apr 19 20:27 .
drwxr-xr-x  3 root root 4.0K Apr 19 20:18 ..
drwxr-xr-x  2 root root 4.0K Apr 19 20:18 Afbeelding
drwxr-xr-x  2 root root 4.0K Apr 19 20:18 Documenten
drwxr-xr-x  2 root root 4.0K Apr 19 20:18 Downloads
drwxr-xr-x  2 root root 4.0K Apr 19 20:18 Muziek
drwxr-xr-x  2 root root 4.0K Apr 19 20:18 scripts
drwxr-xr-x  2 root root 4.0K Apr 19 20:27 test123
drwxr-xr-x  2 root root 4.0K Apr 19 20:27 test456
drwxr-xr-x  2 root root 4.0K Apr 19 20:27 test789
```
In de vorige stap hebben we de testfolders aangemaakt. We willen deze folders verwijderen.

```yml
    - name: verwijder de test folders
      file:
        path: $HOME/home/user1/{{item}}
        state: absent
      loop:
       - test123
       - test456
       - test789
```

```bash
debian# ls -alh /root/home/user1/
total 28K
drwxr-xr-x 7 root root 4.0K Apr 19 20:24 .
drwxr-xr-x 3 root root 4.0K Apr 19 20:18 ..
drwxr-xr-x 2 root root 4.0K Apr 19 20:18 Afbeelding
drwxr-xr-x 2 root root 4.0K Apr 19 20:18 Documenten
drwxr-xr-x 2 root root 4.0K Apr 19 20:18 Downloads
drwxr-xr-x 2 root root 4.0K Apr 19 20:18 Muziek
drwxr-xr-x 2 root root 4.0K Apr 19 20:18 scripts
```

We willen de folders op de folders permissions zetten. Zo willen we op de script folder chmod 0744 (User kan alles, andere enkel lezen) geven en de andere folders 0666 (Iedereen kan lezen en schrijven).
We voegen iets klein aan bij de task "meerder folders voor user1":

```yml 
   - name: meerder folders voor user1
      file:
        path: $HOME/home/user1/{{item}}
        state: directory
        mode: 0666
```
We kunnen de eigenaar van de script specifiëren. Dit is in dit scenario niet echt zinvol aangezien er maar 1 persoon de machine gebruikt (root).

```yml
  - name: chmod scripts
      file:
        path: $HOME/home/user1/scripts
        state: directory
        owner: root
        group: root
        mode: 0744
      become: true
```

Natuurlijk hebben we niks aan folders die leeg zijn. Met de file modules kan je ook bestanden beheren. Dus maken we een aantel bestanden aan.

```yml
    - name: bestanden klaarzetten in scriptfolder
      file:
        path: $HOME/home/user1/scripts/{{item}}
        state: touch # Dit is hetzelfde als touch in terminal
        mode: 0755
      loop:
        - test123.py
        - test456.py
        - test789.py
        - backup.py
        - ftp.py
        - hello.py
```
Zoals je ziet is dit hetzelfde als het aanmaken van folders (chmod, loop, etc). Alleen de state is verandert. Als je de inhoud van de bestanden gaat bekijken, zie je dat deze leeg zijn.

Het verwijderen van bestanden is dus hetzelfde als het verwijderen van folders:

```yml
- name: test bestanden verwijderen
      file:
        path: $HOME/home/user1/scripts/{{item}}
        state: absent
      loop:
        - test123.py
        - test456.py
        - test789.py
```

#### Command module
#### Shell module
#### Copy module
#### Fetch module
#### apt module 
<!-- #### yum module -->