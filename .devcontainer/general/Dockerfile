FROM fedora:latest

RUN dnf update -y

RUN	dnf install -y sudo git curl wget unzip zip nano tmux bash-completion procps dnf-plugins-core

# RUN dnf install -y php php-cli php-fpm php-mbstring php-xml php-json php-pdo php-mysqlnd php-gd php-curl php-zip php-opcache php-intl php-bcmath php-devel

RUN dnf-3 config-manager --add-repo https://download.docker.com/linux/fedora/docker-ce.repo
RUN dnf install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

RUN dnf autoremove -y && dnf clean all
