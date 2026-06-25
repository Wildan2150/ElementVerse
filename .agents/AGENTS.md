# Project Workflow & Testing Rules

These rules govern the development workflow and verification procedures for this workspace.

## 1. Browser Testing Restrictions
- **No Browser Testing by Agent**: The AI agent **MUST NOT** use automated browser subagents (`browser_subagent`) or perform any browser-based visual validation of the application.
- **User-Led Verification**: Browser testing, visual layout checks, and manual feature verification are handled exclusively by the user.
- **Reporting Issues**: If any layout, style, or functional mismatch occurs during verification, the user will report them to the agent using screenshots and text descriptions.

## 2. Verification Guidelines for the Agent
To verify changes, the agent should only use:
- Prettier formatting check (`npx prettier --check [file]`) or write (`npx prettier --write [file]`).
- ESLint checks (`npx eslint [file]`).
- Python or regex-based tag validation to ensure HTML/Vue template nesting is syntactically sound before completing tasks.
