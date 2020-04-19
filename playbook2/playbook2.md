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

We willen nu voor user1 een een aantal folders verschillende folders aanmaken. Dit doen we door gebruik te maken van een lus.

```yml
   - name: meerder folders voor user1
      file:
        path: $HOME/home/user1/{{item}}
        state: directory
      loop:
        - Downloads
        - Documenten
        - Muziek
        - Afbeeldingen
        - test123
        - test456
        - scripts
```

```bash
debian# ls -alh
total 28K
drwxr-xr-x 7 root root 4.0K Apr 19 11:37 .
drwxr-xr-x 3 root root 4.0K Apr 19 11:26 ..
drwxr-xr-x 2 root root 4.0K Apr 19 11:37 Afbeeldingen
drwxr-xr-x 2 root root 4.0K Apr 19 11:37 Documenten
drwxr-xr-x 2 root root 4.0K Apr 19 11:37 Downloads
drwxr-xr-x 2 root root 4.0K Apr 19 11:37 Muziek
drwxr-xr-x 2 root root 4.0K Apr 19 11:37 scripts

```



#### Command module
#### Shell module
#### Copy module
#### Fetch module
#### apt module 
<!-- #### yum module -->