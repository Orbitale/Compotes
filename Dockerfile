FROM debian:10-slim

LABEL maintainer="pierstoval@gmail.com"

ENV GOSU_VERSION=1.11 \
    PATH=/home/.composer/vendor/bin:$PATH \
    RUN_USER="_www"

COPY docker/php/bin/entrypoint.sh /bin/entrypoint
COPY docker/php/etc/php.ini /etc/php/7.4/fpm/conf.d/99-custom.ini
COPY docker/php/etc/php.ini /etc/php/7.4/cli/conf.d/99-custom.ini

RUN set -xe \
    && apt-get update \
    && apt-get upgrade -y --no-install-recommends \
        ca-certificates \
        git \
        curl \
        wget \
        openssh-client \
        unzip \
    \
    && `# Deb Sury PHP repository` \
    && apt-get -y install apt-transport-https lsb-release ca-certificates curl \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    && apt-get update \
    \
    && `# User management for entrypoint` \
    && curl -L -s -o /bin/gosu https://github.com/tianon/gosu/releases/download/${GOSU_VERSION}/gosu-$(dpkg --print-architecture | awk -F- '{ print $NF }') \
    && chmod +x /bin/gosu \
    && mkdir -p /home \
    && groupadd ${RUN_USER} \
    && adduser --home=/home --shell=/bin/bash --ingroup=${RUN_USER} --disabled-password --quiet --gecos "" --force-badname ${RUN_USER} \
    && chown ${RUN_USER}:${RUN_USER} /home \
    \
    && `# PHP and extensions` \
    && apt-get install -y \
        php7.4 \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-fpm \
        php7.4-json \
        php7.4-mbstring \
        php7.4-mysql \
        php7.4-opcache \
        php7.4-readline \
        php7.4-xml \
        php7.4-zip \
    \
    && `# Composer` \
    && (curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer) \
    && runuser -l ${RUN_USER} -c 'composer global require --prefer-dist symfony/flex' \
    \
    && `# Symfony CLI` \
    && (wget https://get.symfony.com/cli/installer -O - | bash) \
    && mv $HOME/.symfony/bin/symfony /usr/local/bin/symfony \
    && symfony server:ca:install \
    \
    && `# Clean apt and remove unused libs/packages to make image smaller` \
    && runuser -l $RUN_USER -c 'composer clearcache' \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false -o APT::AutoRemove::SuggestsImportant=false $BUILD_LIBS \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/www/* /var/cache/* /home/.composer/cache

USER _www

WORKDIR /srv

ENTRYPOINT ["/bin/entrypoint"]

CMD ["symfony", "serve", "--dir=/srv", "--allow-http", "--no-tls", "--port=8000"]

EXPOSE 8000
