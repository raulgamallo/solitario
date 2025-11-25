FROM fedora:latest

# General
RUN	dnf install -y sudo git curl wget unzip zip nano tmux bash-completion procps dnf-plugins-core

# Update
RUN dnf update -y

# Docker
RUN dnf install -y docker-cli containerd docker-compose docker-compose-switch

# RUN dnf install -y php php-cli php-fpm php-mbstring php-xml php-json php-pdo php-mysqlnd php-gd php-curl php-zip php-opcache php-intl php-bcmath php-devel

# Cleanup
RUN dnf autoremove -y && dnf clean all
