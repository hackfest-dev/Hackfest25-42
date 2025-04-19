# NFT Verification Service

A standalone web application for verifying the authenticity of NFTs minted by the authorized NFT minting service.

## Features

- **Transaction Verification**: Verifies if a transaction hash represents a valid NFT minting operation
- **Authenticity Check**: Confirms the NFT was minted by the authorized wallet
- **NFT Details**: Shows detailed information about verified NFTs
- **OpenSea Integration**: Direct links to view NFTs on OpenSea

## Getting Started

### Prerequisites

- Node.js (v14 or higher)
- npm or yarn
- Infura account (for Ethereum RPC access)

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd nft-verifier
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Create a `.env` file in the root directory with the following variables:
   ```
   REACT_APP_SEPOLIA_RPC_URL=https://sepolia.infura.io/v3/YOUR_INFURA_PROJECT_ID
   REACT_APP_INFURA_PROJECT_ID=YOUR_INFURA_PROJECT_ID
   ```

4. Start the development server:
   ```bash
   npm start
   ```

The app will start on port 3001 (http://localhost:3001)

## Usage

1. Enter a transaction hash from an NFT minting operation
2. Click "Verify NFT"
3. The application will check if the transaction:
   - Exists on the Sepolia network
   - Was sent to the NFT contract
   - Was initiated by the authorized wallet
   - Contains a valid NFT transfer event
4. If verified, you'll see details about the NFT and a link to view it on OpenSea

## Contract Information

This application verifies NFTs minted by the following contract on the Ethereum Sepolia testnet:

```
0x8cFe8F5395c87522Ce96915c2B492960bd63633E
```

## Technologies Used

- React
- TypeScript
- Ethers.js
- Infura API

## License

MIT 