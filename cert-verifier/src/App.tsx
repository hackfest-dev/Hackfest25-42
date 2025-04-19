import React, { useState } from 'react';
import { ethers } from 'ethers';
import './App.css';

// Contract address on Sepolia - same as in main app
const contractAddress = '0x8cFe8F5395c87522Ce96915c2B492960bd63633E';

// Contract ABI - same as in main app
const contractABI = [
  "function mint(string memory tokenURI) public returns (uint256)",
  "function owner() public view returns (address)",
  "event Transfer(address indexed from, address indexed to, uint256 indexed tokenId)"
];

// Infura configuration
const INFURA_PROJECT_ID = process.env.REACT_APP_INFURA_PROJECT_ID!;
const SEPOLIA_RPC_URL = process.env.REACT_APP_SEPOLIA_RPC_URL || `https://sepolia.infura.io/v3/${INFURA_PROJECT_ID}`;

function App() {
  const [txHash, setTxHash] = useState<string>('');
  const [validationResult, setValidationResult] = useState<string>('');
  const [isValid, setIsValid] = useState<boolean | null>(null);
  const [loading, setLoading] = useState<boolean>(false);
  const [nftId, setNftId] = useState<string>('');
  const [nftInfo, setNftInfo] = useState<any>(null);

  const handleTxHashChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setTxHash(event.target.value);
  };

  const verifyTransaction = async () => {
    if (!txHash.trim()) {
      setValidationResult('Please enter a certificate transaction hash');
      setIsValid(null);
      return;
    }

    try {
      setLoading(true);
      setValidationResult('');
      setIsValid(null);
      setNftId('');
      setNftInfo(null);

      // Create provider
      const provider = new ethers.providers.JsonRpcProvider(SEPOLIA_RPC_URL);
      
      // Get transaction receipt
      const receipt = await provider.getTransactionReceipt(txHash);
      
      if (!receipt) {
        setValidationResult('Certificate not found on blockchain');
        setIsValid(false);
        return;
      }

      // Create contract instance
      const contract = new ethers.Contract(contractAddress, contractABI, provider);
      
      // Get contract owner
      const owner = await contract.owner();

      // Verify if the transaction was sent to our contract
      if (receipt.to?.toLowerCase() !== contractAddress.toLowerCase()) {
        setValidationResult('This transaction does not relate to our certificate system');
        setIsValid(false);
        return;
      }

      // Get the transaction
      const transaction = await provider.getTransaction(txHash);
      
      // Verify if the transaction was sent by the contract owner
      if (transaction.from.toLowerCase() !== owner.toLowerCase()) {
        setValidationResult('Certificate was not issued by an authorized authority');
        setIsValid(false);
        return;
      }

      // Find the Transfer event in the logs
      const transferEvent = receipt.logs.find((log: any) => {
        try {
          const parsedLog = contract.interface.parseLog(log);
          return parsedLog.name === 'Transfer';
        } catch (e) {
          return false;
        }
      });

      if (!transferEvent) {
        setValidationResult('No certificate issuance found in this transaction');
        setIsValid(false);
        return;
      }

      // Parse the event to get the NFT ID
      const parsedLog = contract.interface.parseLog(transferEvent);
      const tokenId = parsedLog.args[2].toString();
      setNftId(tokenId);

      // Set validation result
      setValidationResult('Verified: This certificate is authentic and was issued by XYZ Organisation');
      setIsValid(true);

      // Create NFT info object
      setNftInfo({
        tokenId,
        to: parsedLog.args[1],
        from: parsedLog.args[0],
        blockNumber: receipt.blockNumber,
        transactionHash: receipt.transactionHash
      });

    } catch (error: any) {
      console.error('Error verifying certificate:', error);
      setValidationResult(`Error verifying certificate: ${error.message}`);
      setIsValid(false);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="App">
      <header className="header">
        <h1>Certificate Verification Portal</h1>
        <p className="subtitle">Verify the authenticity of your blockchain-secured certificates</p>
      </header>
      <main>
        <div className="verification-container">
          <h2>Verify Certificate Authenticity</h2>
          <div className="verification-section">
            <div className="input-group">
              <label htmlFor="tx-hash">Certificate Transaction Hash:</label>
              <input
                id="tx-hash"
                type="text"
                value={txHash}
                onChange={handleTxHashChange}
                placeholder="Enter the transaction hash of your certificate"
                disabled={loading}
              />
            </div>
            <button onClick={verifyTransaction} disabled={!txHash.trim() || loading} className="verify-button">
              {loading ? 'Verifying...' : 'Verify Certificate'}
            </button>
          </div>

          {validationResult && (
            <div className={`validation-result ${isValid ? 'valid' : 'invalid'}`}>
              <h3>{validationResult}</h3>
              {isValid && nftId && (
                <div className="nft-details">
                  <p>Certificate ID:  <span className="highlight">{nftId}</span></p>
                  <p>View Certificate: <a href={`https://testnets.opensea.io/assets/sepolia/${contractAddress}/${nftId}`} target="_blank" rel="noopener noreferrer" className="opensea-link">View on OpenSea</a></p>
                  {nftInfo && (
                    <>
                      <p>Issued by: <span className="address">{nftInfo.from === ethers.constants.AddressZero ? 'XYZ Organisation' : nftInfo.from}</span></p>
                      <p>Issuance Block: {nftInfo.blockNumber}</p>
                      <p>Verification Link: <a href={`https://sepolia.etherscan.io/tx/${nftInfo.transactionHash}`} target="_blank" rel="noopener noreferrer" className="etherscan-link">View on Blockchain Explorer</a></p>
                    </>
                  )}
                </div>
              )}
            </div>
          )}
        </div>
      </main>
      <footer>
        <p>Certificate Verification System &copy; {new Date().getFullYear()}</p>
      </footer>
    </div>
  );
}

export default App; 