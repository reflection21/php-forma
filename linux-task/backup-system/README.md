### 1. Нужно скачать cdlive или ubuntu desktop запустить в режиме востановления

- При запуске выбираем ( Try ubuntu )

### 2. Новый сервер должен иметь доступ к интернету что бы скачать ssh для переноса через rsync, если есть ssh сделать так что бы сервера находились в одной сети

- nano /etc/netplan/01-network.yml
- netplan apply

### 3. Качаем ссш если нету

- apt update && apt install openssh-server
- vim /etc/ssh/sshd_config

| PublicAuthentication  | yes |
| --- | --- |
| PasswordAuthentication  | yes |
| PerminRootLogin  | yes |
- systemctl restart ssh
- задаем пароль руту passwd
- прокидываем ключ на сервер для rsync  ssh-copy-id -i ~/.ssh/rsync.pub [root@195.26.87.153](mailto:root@195.26.87.153)

### 4. Разбиваем диск для новой системы

- gdisk /dev/sda
- +1M EFO2
- +all 8304
- mkfs.ext4 /dev/sda2
- mkdir /mnt/new_root && mount /dev/sda2 /mnt/new_root

### 5. Переносим / на новый сервер

- rsync -aAXHv \
--exclude={"/dev/","/proc/","/sys/","/tmp/","/run/","/mnt/","/media/*","/lost+found"} \  нужно смотреть 
/  [root@195.26.87.153](mailto:root@195.26.87.153):/mnt/new_root

### 6. Востанавливаем систему

- mkdir /mnt/new_root/dev
mkdir /mnt/new_root/proc
mkdir /mnt/new_root/sys
mkdir /mnt/new_root/run
- mount --bind /dev /mnt/new_root/dev
 mount --bind /proc /mnt/new_root/proc
 mount --bind /sys /mnt/new_root/sys
 mount --bind /run /mnt/new_root/run
- chroot /mnt/new_root
- grub-install /dev/sda
- update-grub
- vim /etc/fstab - примаунтить диск
- blkid
- mount -a
- exit
- umount /mnt/new_root/run
umount /mnt/new_root/sys
umount /mnt/new_root/proc
umount /mnt/new_root/dev
- umount  /mnt/new_root
- reboot

## Errors

# update-initramfs -u

# sudo fsck /dev/sda2 -f -y


**ALT Linux Regular Rescue LiveCD**. Вот ключевой момент:

- **Regular Rescue** — это облегчённый образ для восстановления системы. Он **загружается в squashfs** и работает как “чужая” файловая система.
- GRUB внутри такого LiveCD **не видит ext4 разделы полностью**. Поэтому команды `grub-probe` и `grub-install` часто выдают `unknown filesystem`.
- Ты можешь копировать файлы GRUB (`grub-install --boot-directory=/mnt/boot /dev/sda`) — это работает. Но **генерация конфигурации grub.cfg** через `grub-mkconfig` обычно не получится, и система после перезагрузки может не загрузиться.