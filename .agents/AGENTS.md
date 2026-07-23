# Workspace Rules & Architectural Guidelines — SFI Livestock Apps

## Repository Hygiene
- **Keep Repository Root Clean**: Always keep the repository root directory clean. Do NOT create or store markdown documentation, phase registers, scratch scripts, or report files at the repository root level.
- **Documentation Location**: All documentation, PRDs, governance registers, audit responses, and phase notes MUST be placed inside the `docs/` directory (e.g., `docs/governance/`, `docs/sfi-progress/`).
- **Ignore Scratch / Build Files**: Ensure temporary packaging scripts, SQL dumps, and build output directories (`build/`, `*.zip`, `.db`) are ignored in `.gitignore`.
