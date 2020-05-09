# Playbook 2

In deze playbook gaan we gebruiken maken van een aantal praktische modules bij het beheren van verschillende soorten servers. We maken gebruiken van de volgende modules:

* [File module](#file-module)
* [Copy module](#copy-module)
* [Fetch module](#fetch-module)
* [apt module](#apt-module)
* [Shell & Command module](#Shell-&-Command-module)


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

###### Folders beheren

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

###### Permissions owners 

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

###### Bestanden beheren

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
Documentatie: https://docs.ansible.com/ansible/latest/modules/file_module.html

#### Copy module

###### Bestanden kopiëren

In dit deel gaan we verder op de folderstructuur vanuit de File module. We gaan bestanden kopieren van de lokale host naar al onze hosts met de copy module.

Maak de folder "files" & playbook "copy.yml" aan. De files folder is de standaard folder waar ansible gaat kijken. 

copy.yml:
```yml
- name: bestanden kopiëren.
  hosts: "*"
  tasks:
      - name: Kopieren van 1 simpel bestand
        copy: 
          src: Bestand1.txt
          dest: /root/home/user1/Documenten/
```

```bash
debian# cat Bestand1.txt
Dit is een simpel bestand
```

```yml
- name: bestanden kopiëren.
  hosts: "*"
  tasks:
      - name: Kopieren van meerder bestanden
        copy: 
          src: "{{ item }}"
          dest: "/root/home/user1/Documenten/{{ item }}"
        loop:
          - Bestand1.txt
          - Bestand2.js
          - Bestand3.html
```

1 bestand kopiëren is te specfiek. Vaak wil je meerder bestanden of volledige folders kopiëren naar de andere hosts. Als je meerder bestanden wilt kopiëren moet je gebruik maken van een loop, dit is gelijkaardig aan meerde folders aanmaken.

###### Bestand wijzigen

De inhoud van Bestand1.txt moet verandert worden naar `Ik heb de inhoud veranderd`. Dit kunnen we met de copy module:

```yml
  - name: Verander inhoud Bestand1.txt
        copy:
          dest: /root/home/user1/Documenten/Bestand1.txt
          content: "Ik heb de inhoud veranderd \n"
      
```

###### Folders kopiëren

In de folder **bongo-cat-codes-2jamming** zit een website die ik in de documenten van elke host wil steken.

*Pas om met de / dit kopieert oftewel de inhoud van de map of de volledige map*

```yml
- name: Kopieer website naar machine
        copy:
        # bongo-cat-codes-2jamming/ != bongo-cat-codes-2jamming
          src: bongo-cat-codes-2jamming 
          dest: "/root/home/user1/Documenten"
```

```bash
debian# tree
.
|-- Bestand1.txt
|-- Bestand2.js
|-- Bestand3.html
`-- bongo-cat-codes-2jamming
    |-- README.markdown
    |-- dist
    |   |-- index.html
    |   |-- script.js
    |   `-- style.css
    |-- license.txt
    `-- src
        |-- index.pug
        |-- script.babel
        `-- style.scss

3 directories, 11 files
```

Ik zou graag een backup willen maken van mijn vim config bestanden op de host zelf. Dus alle bestanden van **/etc/vim/** moeten gekopieerd worden naar **/root/home/user1/Documenten/VimCopy/**

Het is belangrijk dat je `remote_src: true` aanzet, anders worden jouw lokale configs gekopieerd naar de machines.

```yml
- name: maak Vim backup
        copy:
          src: /etc/vim
          dest: "/root/home/user1/Documenten/vimCopy"
          remote_src: true 
```

#### Fetch module

Als we bestanden en folders van de machines willen kopiëren, moeten we gebruik maken van de FETCH module.

In de playbook fetch.yml, maken we een bestand aan dat we daarna gaan binnen halen.

```yml

- name: Bestanden van de machines binnehalen
  hosts: "*"
  tasks:
      - name: Maak nieuw bestand aan
        file:
          path: /root/home/user1/Documenten/HaalMeBinnen.txt
          state: touch

      - name: Inhoud bestand
        copy: 
          dest: /root/home/user1/Documenten/HaalMeBinnen.txt
          content: "Haal dit bestand binnen met de FETCH module."

      - name: Binnenhalen
        fetch:
          src: /root/home/user1/Documenten/HaalMeBinnen.txt
          dest: binnen
```

Als je deze playbook draait, krijg je het volgende resultaat:

```bash
tibuaksi@tibauski ~/D/s/S/playbook2 (master)> tree binnen/
binnen/
├── 172.17.0.2
│   └── root
│       └── home
│           └── user1
│               └── Documenten
│                   └── HaalMeBinnen.txt
└── 172.17.0.3
    └── root
        └── home
            └── user1
                └── Documenten
                    └── HaalMeBinnen.txt
```

Zoals je kan zijn de bestanden binnengehaald. Dit kan redelijk onoverzichtelijk worden als je meerder bestanden van meerdere hosts wilt gaan kopiëren. Je kan het volgende veranderen:

Verwijder eerst de folder "binnen"

```yml
- name: Binnenhalen
        fetch:
          src: /root/home/user1/Documenten/HaalMeBinnen.txt
          dest: "binnen/{{ inventory_hostname }}/"
          flat: true
```

```bash
tibuaksi@tibauski ~/D/s/S/playbook2 (master)> tree binnen/
binnen/
├── 172.17.0.2
│   └── HaalMeBinnen.txt
└── 172.17.0.3
    └── HaalMeBinnen.txt
```
Dit is een stuk overzichtelijker dan het vorige. Het is dus belangrijk dat je de hosts op een manier kunt identificiëren (bv. met inventory_hostname).

Je kan ook met loop werken om meerdere bestanden binnen te halen.
Bij folders met je dan de / achter je pad zetten

```yml
- name: Binnenhalen
  fetch:
    src: /root/home/user1/Documenten/{{ item }}
    dest: "binnen/{{ inventory_hostname }}/"
    flat: true
  loop:
    - HaalMeBinnen.txt
    - HaalMeBinnen2.txt
        
```

#### apt module 

Met de apt module kunnen we de packages beheren van al onze debian-based machines.

###### Packages installeren

We maken gebruik van de apt module voor debian-based distro's. Er zijn verschillende Ansible modules voor het beheren van packages zoals de yum, pacman, ...

We beginnen met het installeren van de packages **net-tools**. In de playbook apt.yml zie je dat we de playbook niet tegen alle hosts gaan uitvoeren. In de inventory hebben we alle debian-based machines in dezelfde groep gestoken. 

Als je een inventory hebt met hosts die niet debian-based zijn (redhat, arch, gentoo, ...) gaat deze playbook niet kunnen worden uitgevoerd. Je kan dat oplossen door gebruik te maken van "ansible_os_family".

*Inventory heeft enkel debian-based machines*

```yml
- name: Packages beheren op alle machines
  hosts: "debian-based" #inventory group
  tasks:
    - name: Instaleren van net-tools
      apt: 
        name: net-tools
        state: present #installeren
        update_cache: true #gaat apt cache updaten
      become: true #root permissions nodig
```

*Inventory bestaat uit verschillende soorten machines*

```yml
- name: Packages beheren op alle machines
  hosts: "*" #alle hosts
  tasks:
    - name: Instaleren van net-tools
      apt: 
        name: net-tools
        state: present #installeren
        update_cache: true #gaat apt cache updaten
      become: true #root permissions nodig 
      #wordt enkel uitgevoord voor debian machines
      when: ansible_os_family == 'Debian'
```
Resultaat:

```bash
debian# ifconfig --version
net-tools 2.10-alpha
```
###### Packages verwijderen

We kunnen makkelijk meerder packages in één keer installeren door:

```yml
- name: Meerder packages installeren
      apt:
        name: 
          - cowsay
          - mariadb-server
        state: present #installeren
        update_cache: true #gaat apt cache updaten
      become: true #root permissions nodig
```

Sommige packages zijn zinvoller dan andere. We kunnen makkelijk packages verwijderen door:

```yml
    - name: Cowsay verwijderen
      apt:
          name: cowsay
          state: absent #verwijderen
          #verwijdert alle redudante dependencies van de packages apt-get autoremove
          autoremove: true 
        become: true 
```

###### Packages updaten en upgraden

Na een tijd moeten onze packages worden geupdate. Het is belangrijk dat je eerst de lijst van beschikbare packages update met apt-get update. apt-get update instaleert niet de packages. Na het updaten van de lijst met packages wil je deze installeren met apt-get upgrade. Je wilt de laatste packages hebben, daar doe je eerst een update voordat je upgrade.

```yml
    #apt-get update
    - name: Update apt repo en cache 
      apt: update_cache=yes force_apt_get=yes 

    #apt-get upgrade
    - name: Upgrade alle packages
      apt: upgrade=dist force_apt_get=yes
```
force_apt_get forceert de update\upgrade van de packages (hetzelfde als -f optie).

#### Shell & Command module

We hebben in playbook 1 al gebruik gemaakt van de shell module om een simpel bestand met tekst weg te schrijven. De shell module is gelijkaardig aan het uitvoeren van commando's in bash. 

De command module en de shell module lijken op elkaar. Toch is er verschil tussen de beiden. Het verschil wordt duidelijk als we naar een simpel voorbeeld kijken. Namelijk het aanmaken van een bestand.

**Shell module**
```yml
    - name: Commando met shell module
      shell: echo "Dit bestand is gemaakt met de shell module" > /root/home/user1/Documenten/shell.txt
```
**Command module**

```yml
    - name: Commando met command module
      command: echo "Dit bestand is gemaakt met de command module" > /root/home/user1/Documenten/command.txt
```

We voeren de command.yml playbook uit en zien het volgende in onze containers:

```bash
ubuntu# cat root/home/user1/Documenten/*
Dit bestand is gemaakt met de shell module
```

We zien dat enkel de shell module een bestand met tekst heeft aangemaakt. Dit is omdat de command module de karakters 'escaped'. Het grote verschil tussen de shell en de command module is dat de command module geen gebruik maakt van speciale karaters ( ! | && // ) en deze behandelt als een string. Dit maakt de command module een stuk veiliger.

We kunnen met de command module gebruik maken van argumenten en variabelen.

```yml
    - name: Gebruiken van argumeten en variabelen.
      command: touch $BESTAND
      args:
        chdir: $FOLDER
      environment:
        FOLDER: /tmp/
        BESTAND: CommandVariabele
```

```bash
ubuntu# ls
CommandVariabele  shell.txt
```

In envirmonment initialiseren we onze variabelen. FOLDER = het pad van het bestand & BESTAND = het de naam van het bestand. De variabele FOLDER wordt meegegeven aan het changedirectory argumetent (chdir). De command module heeft [verschillende argumenten](https://docs.ansible.com/ansible/latest/modules/command_module.html) (cmd,stdin, war,..).

In vorige modules hebben we gezien hoe we dezelfde taak meerder keren kunnen uitvoeren. Ook bij de command module kan je meerder commando's uitvoeren door gebruik te maken van loop.

Een klein voorbeeld:
```yml
 - name: Meerder commando's in 1 task
      command: "{{ item }}"
      loop: 
        - touch /root/home/user1/Documenten/geheimBestand
        - mkdir /root/home/user1/Documenten/geheimeFolder
        - mv /root/home/user1/Documenten/geheimBestand /root/home/user1/Documenten/geheimeFolder
        - chmod +x /root/home/user1/Documenten/geheimBestand
```