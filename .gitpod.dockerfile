FROM gitpod/workspace-full

RUN wget https://get.symfony.com/cli/installer -O - | bash
ENV PATH="$PATH:$HOME/.symfony/bin"
