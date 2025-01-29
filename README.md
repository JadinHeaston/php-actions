# PHP Actions

This is a repository dedicated to PHP CLI scripts that can (often) be used as Git actions.

## Code Guidelines

- Scripts should strive to be entirely self-contained where possible.
  - Some may have external dependencies. Limit dependencies where possible and do NOT use Composer.

> [!NOTE]
> The [includes directory](./includes/) contains shared code, although this code is directly copied into the script itself.  
> Modifications to one version of a function should be reflected in all others. If the version needs to be distinct, the name needs to be changed.

## Deployment

1. Create a `.workflows` directory in the root of your project.
2. Copy the desired script directory into `.workflows`
3. [Git Hook](#git-hooks)
4. Actions
    1. Symbolically link the `yaml` files into the appropriate directory.
      - Command: `ln -sf /path/to/project/.workflows/{script_folder}/{workflow.yaml} /path/to/project/.{hoster}/{workflow.yaml}`
        - Ensure you use full paths!
      - Github: `.github/workflows`
      - Gitea: `.gitea/workflows`

## Git Hooks

[Git Hooks](https://git-scm.com/book/ms/v2/Customizing-Git-Git-Hooks) are client side scripts that can run based on certain Git events.

1. Using **Git Bash** (or any Linux terminal), create a symbolic link from the corresponding hook script(s) into the `.git/hooks/{hook_name}` directory.
    - Command: `ln -sf /path/to/project/.workflows/{script_folder}/{hook_name} /path/to/project/.git/hooks/{hook_name}`
      - Ensure you use full paths!

> [!IMPORTANT]  
> It's important that the hook script itself is executable.
> You can use the following command: `chmod +x <HOOK_NAME>`

> [!NOTE]  
> If multiple pre-commit scripts are used, custom modification may be required.

## Actions

### Github Actions

Github requires that the workflow (`.yaml` file) is placed under: `.github/workflows/`. Ensure it is created.

Symbolically link the `yaml` files into that directory.

### Gitea Actions

Gitea requires that the workflow (`.yaml` file) is placed under: `.gitea/workflows/`. Ensure it is created.

Symbolically link the `yaml` files into that directory.
